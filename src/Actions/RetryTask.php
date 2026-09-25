<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\ExecutionType;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkflowStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\Transition;
use RuntimeException;

class RetryTask
{
    public function execute(
        Task $task,
        ?Model $actor = null,
    ): Task {
        return DB::transaction(function () use (
            $task,
            $actor,
        ): Task {
            $task = Task::query()
                ->lockForUpdate()
                ->findOrFail($task->getKey());

            if ($task->status !== TaskStatus::Failed) {
                throw new RuntimeException(
                    'Only failed tasks may be retried.'
                );
            }

            $task->workflowInstance->update([
                'status' => WorkflowStatus::Active,
                'failed_at' => null,
                'failure_reason' => null,
            ]);

            $task->update([
                'status' => TaskStatus::Ready,
                'attempt' => $task->attempt + 1,
                'ready_at' => now(),
                'started_at' => null,
                'completed_at' => null,
                'failed_at' => null,
                'failure_reason' => null,
            ]);

            Transition::query()->create([
                'workflow_instance_id' => $task->workflow_instance_id,

                'from_task_id' => $task->getKey(),
                'from_workpackage_slug' => $task->workpackage_slug,

                'event' => 'task_retried',
                'actor_type' => $actor?->getMorphClass(),
                'actor_id' => $actor?->getKey(),
                'payload' => [
                    'attempt' => $task->attempt,
                ],
                'performed_at' => now(),
            ]);

            if ($task->execution_type === ExecutionType::Rimba) {
                return app(ExecuteRimbaTask::class)
                    ->execute($task);
            }

            return $task->fresh();
        });
    }
}
