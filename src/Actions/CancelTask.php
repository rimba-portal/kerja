<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\Transition;
use RuntimeException;

class CancelTask
{
    public function execute(
        Task $task,
        ?Model $actor = null,
        ?string $reason = null,
    ): Task {
        if (in_array($task->status, [
            TaskStatus::Completed,
            TaskStatus::Cancelled,
        ], true)) {
            throw new RuntimeException(
                "Task [{$task->uuid}] cannot be cancelled."
            );
        }

        $task->update([
            'status' => TaskStatus::Cancelled,
            'cancelled_at' => now(),
            'failure_reason' => $reason,
        ]);

        Transition::query()->create([
            'workflow_instance_id' => $task->workflow_instance_id,

            'from_task_id' => $task->getKey(),
            'from_workpackage_slug' => $task->workpackage_slug,

            'event' => 'task_cancelled',
            'actor_type' => $actor?->getMorphClass(),
            'actor_id' => $actor?->getKey(),
            'payload' => [
                'reason' => $reason,
            ],
            'performed_at' => now(),
        ]);

        return $task->fresh();
    }
}
