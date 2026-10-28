<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Data\ExecutionResult;
use Rimba\Work\Enums\ExecutionStatus;
use Rimba\Work\Events\OfferingExecuted;
use Rimba\Work\Events\OfferingExecutionFailed;
use Rimba\Work\Events\OfferingExecutionStarted;
use Rimba\Work\Events\OfferingSubjectCreated;
use Rimba\Work\Events\WorkflowStarted;
use Rimba\Work\Models\Offering;
use Rimba\Work\Models\OfferingExecution;
use Throwable;

class ExecuteOffering
{
    public function __construct(
        protected PrepareOffering $prepare,
        protected ValidateOfferingInput $validateInput,
        protected MapOfferingInput $mapInput,
        protected CreateOfferingSubject $createSubject,
        protected RecordOfferingExecution $recordExecution,
        protected StartOfferingWorkflow $startWorkflow,
        protected CompleteOfferingExecution $completeExecution,
    ) {}

    public function execute(
        Offering $offering,
        array $input = [],
        array $metadata = [],
        ?Model $existingSubject = null,
        ?Model $formSubmission = null,
        bool $alreadyValidated = false,
        ?bool $queue = null,
    ): ExecutionResult {
        $preparedOffering = $this->prepare->execute(
            offering: $offering,
            metadata: $metadata,
        );

        $validated = $this->validateInput->execute(
            offering: $offering,
            input: $input,
            alreadyValidated: $alreadyValidated,
        );

        $mappedOfferingData = $this->mapInput->execute(
            offering: $offering,
            input: $validated,
            context: $preparedOffering->context,
        );

        $execution = null;

        try {
            return $this->transaction(function () use (
                $offering,
                $preparedOffering,
                $validated,
                $mappedOfferingData,
                $existingSubject,
                $formSubmission,
                $queue,
                &$execution,
            ): ExecutionResult {
                $execution = $this->recordExecution->execute(
                    offering: $offering,
                    input: $validated,
                    context: $preparedOffering->context,
                    mapped: $mappedOfferingData,
                    formSubmission: $formSubmission,
                );

                event(new OfferingExecutionStarted(
                    execution: $execution,
                ));

                $subject = $this->createSubject->execute(
                    offering: $offering,
                    subjectClass: $preparedOffering->subjectClass,
                    mapped: $mappedOfferingData,
                    existingSubject: $existingSubject,
                );

                if ($subject instanceof Model) {
                    $execution->forceFill([
                        'subject_type' => $subject->getMorphClass(),
                        'subject_id' => $subject->getKey(),
                    ])->save();

                    event(new OfferingSubjectCreated(
                        execution: $execution,
                        subject: $subject,
                    ));
                }

                $execution->markRunning();

                $workflowRun = $this->startWorkflow->execute(
                    offering: $offering,
                    execution: $execution,
                    subject: $subject,
                    context: $preparedOffering->context,
                    queue: $queue,
                );

                $workflowQueued = $offering->requiresWorkflow()
                    && ! $workflowRun instanceof Model
                    && $this->isQueued($queue);

                if ($offering->requiresWorkflow()) {
                    event(new WorkflowStarted(
                        execution: $execution,
                        workflowRun: $workflowRun,
                        queued: $workflowQueued,
                    ));
                }

                $resultSnapshot = [
                    'offering_code' => $offering->code,

                    'subject' => $subject instanceof Model
                        ? [
                            'type' => $subject->getMorphClass(),
                            'id' => $subject->getKey(),
                        ]
                        : null,

                    'workflow_code' => $offering->workflow_code,

                    'workflow_run' => $workflowRun instanceof Model
                        ? [
                            'type' => $workflowRun->getMorphClass(),
                            'id' => $workflowRun->getKey(),
                        ]
                        : null,

                    'workflow_queued' => $workflowQueued,
                ];

                $execution = $this->completeExecution->execute(
                    execution: $execution,
                    subject: $subject,
                    workflowRun: $workflowRun,
                    result: $resultSnapshot,
                    workflowQueued: $workflowQueued,
                );

                $executionResult = new ExecutionResult(
                    execution: $execution,
                    subject: $subject,
                    workflowRun: $workflowRun,
                    formSubmission: $formSubmission,
                    queued: $workflowQueued,
                );

                event(new OfferingExecuted(
                    result: $executionResult,
                ));

                return $executionResult;
            });
        } catch (Throwable $throwable) {
            $execution = $this->recordFailure(
                offering: $offering,
                execution: $execution,
                input: $validated,
                context: $preparedOffering->context->toArray(),
                mapped: $mappedOfferingData->attributes,
                exception: $throwable,
            );

            event(new OfferingExecutionFailed(
                execution: $execution,
                exception: $throwable,
            ));

            throw $throwable;
        }
    }

    protected function transaction(
        callable $callback,
    ): mixed {
        if (! config('work.execution.transactional', true)) {
            return $callback();
        }

        return DB::transaction($callback);
    }

    protected function isQueued(
        ?bool $queue,
    ): bool {
        if ($queue !== null) {
            return $queue;
        }

        return (bool) config(
            'work.providers.workflows.drivers.packstub.queue',
            false,
        );
    }

    protected function recordFailure(
        Offering $offering,
        ?OfferingExecution $execution,
        array $input,
        array $context,
        array $mapped,
        Throwable $exception,
    ): OfferingExecution {
        if (
            $execution instanceof OfferingExecution
            && OfferingExecution::query()
                ->whereKey($execution->getKey())
                ->exists()
        ) {
            return $execution->markFailed(
                message: $exception->getMessage(),
                code: $exception::class,
            );
        }

        return OfferingExecution::query()->create([
            'offering_code' => $offering->code,

            'user_id' => data_get($context, 'user.id'),
            'org_team_id' => data_get($context, 'team.id'),

            'status' => ExecutionStatus::Failed,

            'input_snapshot' => config(
                'work.execution.store_input_snapshot',
                true,
            )
                ? $input
                : null,

            'context_snapshot' => config(
                'work.execution.store_context_snapshot',
                true,
            )
                ? $context
                : null,

            'mapped_snapshot' => config(
                'work.execution.store_mapped_snapshot',
                true,
            )
                ? $mapped
                : null,

            'failure_code' => $exception::class,
            'failure_message' => $exception->getMessage(),
            'failed_at' => now(),
        ]);
    }
}
