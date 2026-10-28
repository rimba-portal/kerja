<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\ExecutionStatus;
use Rimba\Work\Events\WorkCompleted;
use Rimba\Work\Events\WorkflowCancelled;
use Rimba\Work\Events\WorkflowCompleted;
use Rimba\Work\Models\OfferingExecution;

class WorkflowRunSynchronizer
{
    public function synchronize(
        OfferingExecution $execution,
        Model $workflowRun,
    ): OfferingExecution {
        $status = $this->statusValue(
            data_get($workflowRun, 'status'),
        );

        $execution->forceFill([
            'workflow_instance_type' => $workflowRun->getMorphClass(),
            'workflow_instance_id' => $workflowRun->getKey(),

            'result_snapshot' => array_replace_recursive(
                $execution->result_snapshot ?? [],
                [
                    'workflow_run' => [
                        'type' => $workflowRun->getMorphClass(),
                        'id' => $workflowRun->getKey(),
                        'status' => $status,
                    ],
                ],
            ),
        ]);

        if ($this->isSuccessful($status)) {
            $execution->forceFill([
                'status' => ExecutionStatus::Completed,
                'completed_at' => now(),
                'failed_at' => null,
                'cancelled_at' => null,
            ])->save();

            event(new WorkflowCompleted(
                execution: $execution,
                workflowRun: $workflowRun,
            ));

            event(new WorkCompleted(
                execution: $execution,
                subject: $execution->subject,
            ));

            return $execution->refresh();
        }

        if ($this->isFailed($status)) {
            $execution->forceFill([
                'status' => ExecutionStatus::Failed,
                'failure_code' => 'workflow_failed',
                'failure_message' => (string) data_get(
                    $workflowRun,
                    'error',
                    'The workflow run failed.',
                ),
                'failed_at' => now(),
            ])->save();

            return $execution->refresh();
        }

        if ($this->isCancelled($status)) {
            $execution->forceFill([
                'status' => ExecutionStatus::Cancelled,
                'cancelled_at' => now(),
            ])->save();

            event(new WorkflowCancelled(
                execution: $execution,
                workflowRun: $workflowRun,
            ));

            return $execution->refresh();
        }

        $execution->forceFill([
            'status' => ExecutionStatus::Running,
        ])->save();

        return $execution->refresh();
    }

    protected function statusValue(
        mixed $status,
    ): ?string {
        if ($status instanceof BackedEnum) {
            return (string) $status->value;
        }

        return is_string($status)
            ? strtolower($status)
            : null;
    }

    protected function isSuccessful(
        ?string $status,
    ): bool {
        return in_array(
            $status,
            ['success', 'succeeded', 'completed'],
            true,
        );
    }

    protected function isFailed(
        ?string $status,
    ): bool {
        return in_array(
            $status,
            ['failed', 'error'],
            true,
        );
    }

    protected function isCancelled(
        ?string $status,
    ): bool {
        return in_array(
            $status,
            ['cancelled', 'canceled'],
            true,
        );
    }
}
