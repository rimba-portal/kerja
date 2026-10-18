<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\FlowInstanceStatus;
use Rimba\Work\Enums\HookPoint;
use Rimba\Work\Models\Flow;
use Rimba\Work\Models\FlowInstance;
use Rimba\Work\Services\FlowInitiatorService;
use Rimba\Work\Services\HookBindingRunner;
use Rimba\Work\Services\LifecycleService;
use Rimba\Work\Services\TaskEventLogger;
use Rimba\Work\Support\HookContext;
use RuntimeException;

final class StartFlow
{
    public function __construct(
        private FlowInitiatorService $flowInitiatorService,
        private CreateTask $createTask,
        private TaskEventLogger $taskEventLogger,
        private HookBindingRunner $hookBindingRunner,
        private LifecycleService $lifecycleService,
    ) {}

    public function execute(
        Flow $flow,
        Model $initiator,
        ?Model $subject = null,
        array $formData = [],
    ): FlowInstance {
        if (! $this->flowInitiatorService->mayStart(
            $initiator,
            $flow,
        )) {
            throw new RuntimeException(
                'Initiator is not permitted to start this Flow.'
            );
        }

        $step = $flow->startStep()
            ?? throw new RuntimeException(
                'Flow has no start Step.'
            );

        return DB::transaction(function () use (
            $flow,
            $initiator,
            $subject,
            $formData,
            $step,
        ): FlowInstance {
            $flowInstance = FlowInstance::query()->create([
                'workflow_id' => $flow->id,
                'workflow_version' => $flow->version,
                'initiator_type' => $initiator->getMorphClass(),
                'initiator_id' => $initiator->getKey(),
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'current_workflow_step_id' => $step->id,
                'status' => FlowInstanceStatus::Active,
                'form_data' => $formData,
                'traveller' => [],
                'started_at' => now(),
            ]);

            $this->lifecycleService->ensureTracking($flowInstance);

            $hookContext = new HookContext(
                instance: $flowInstance,
                step: $step,
                actor: $initiator,
            );

            $this->hookBindingRunner->runFor(
                $flow,
                HookPoint::WorkflowLoad,
                $hookContext,
            );

            $this->hookBindingRunner->runFor(
                $step,
                HookPoint::StepEnter,
                $hookContext,
            );

            $task = $this->createTask->execute(
                $flowInstance,
                $step,
            );

            $this->taskEventLogger->log(
                $flowInstance,
                'flow_started',
                $initiator,
                $task,
            );

            return $flowInstance->fresh();
        });
    }
}
