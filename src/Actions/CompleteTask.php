<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Artifact;
use Rimba\Work\Models\Task;
use Rimba\Work\Services\WorkflowContextService;
use RuntimeException;

class CompleteTask
{
    public function __construct(
        private WorkflowContextService $workflowContextService,
    ) {}

    public function execute(
        Task $task,
        array $result = [],
        ?Model $actor = null,
    ): Task {
        return DB::transaction(function () use (
            $task,
            $result,
            $actor,
        ): Task {
            $task = Task::query()
                ->lockForUpdate()
                ->findOrFail($task->getKey());

            if (! in_array($task->status, [
                TaskStatus::Ready,
                TaskStatus::Assigned,
                TaskStatus::Started,
                TaskStatus::Waiting,
            ], true)) {
                throw new RuntimeException(
                    "Task [{$task->uuid}] cannot be completed from status [{$task->status->value}]."
                );
            }

            $task->update([
                'status' => TaskStatus::Completed,
                'result' => $result,
                'completed_at' => now(),
                'failure_reason' => null,
            ]);

            foreach ($result['artifacts'] ?? [] as $artifact) {
                Artifact::query()->create([
                    'workflow_instance_id' => $task->workflow_instance_id,

                    'produced_by_task_id' => $task->getKey(),

                    'key' => $artifact['key'],
                    'name' => $artifact['name']
                        ?? str($artifact['key'])->headline(),

                    'type' => $artifact['type'] ?? 'data',
                    'value' => $artifact['value'] ?? null,
                    'metadata' => $artifact['metadata'] ?? null,
                    'produced_at' => now(),
                ]);
            }

            if (is_array($result['context'] ?? null)) {
                $this->workflowContextService->merge(
                    $task->workflowInstance,
                    $result['context'],
                );
            }

            app(AdvanceWorkflow::class)->execute(
                $task->workflowInstance->fresh(),
                $task->fresh(),
                $actor,
            );

            return $task->fresh();
        });
    }
}
