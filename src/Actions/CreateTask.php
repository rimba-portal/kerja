<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\ExecutionType;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\WorkflowInstance;
use Rimba\Work\Services\ActorResolverService;

class CreateTask
{
    public function __construct(
        private ActorResolverService $actorResolverService,
    ) {}

    public function execute(
        WorkflowInstance $workflow,
        array $workPackage,
        ?Model $initiator = null
    ): Task {
        $executionType = ExecutionType::from(
            strtolower($workPackage['execution_type'])
        );

        $assignee = $this->actorResolverService->resolve(
            $workPackage,
            $initiator,
            $workflow->context ?? []
        );

        $status = match ($executionType) {
            ExecutionType::Human => $assignee
                ? TaskStatus::Assigned
                : TaskStatus::Ready,
            ExecutionType::Rimba => TaskStatus::Ready,
            ExecutionType::EventDriven => TaskStatus::Waiting,
        };

        $model = $workflow->tasks()->create([
            'workpackage_slug' => $workPackage['slug'],
            'workpackage_snapshot' => $workPackage,
            'execution_type' => $executionType,
            'activity_type' => strtolower($workPackage['activity_type']),
            'business_object' => $workPackage['business_object'],
            'actor' => $workPackage['actor'],
            'assignee_type' => $assignee?->getMorphClass(),
            'assignee_id' => $assignee?->getKey(),
            'status' => $status,
            'suppliers' => $workPackage['suppliers'] ?? [],
            'inputs' => $workPackage['inputs'] ?? [],
            'outputs' => $workPackage['outputs'] ?? [],
            'customers' => $workPackage['customers'] ?? [],
            'handler' => $workPackage['handler'] ?? null,
            'trigger_event' => $workPackage['trigger_event'] ?? null,
            'ready_at' => $status === TaskStatus::Ready
                ? now()
                : null,
            'assigned_at' => $status === TaskStatus::Assigned
                ? now()
                : null,
            'waiting_at' => $status === TaskStatus::Waiting
                ? now()
                : null,
        ]);

        if ($executionType === ExecutionType::Rimba) {
            app(ExecuteRimbaTask::class)->execute($model);
        }

        return $model->fresh();
    }
}
