<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Services\TaskEligibilityService;
use Rimba\Work\Services\TaskEventLogger;
use RuntimeException;

final class ClaimTask
{
    public function __construct(
        private TaskEligibilityService $taskEligibilityService,
        private TaskEventLogger $taskEventLogger,
    ) {}

    public function execute(Task $task, Model $actor): Task
    {
        return DB::transaction(function () use ($task, $actor): Task {
            $task = Task::query()->lockForUpdate()->findOrFail($task->getKey());

            if ($task->status !== TaskStatus::Ready || $task->assignee_id !== null) {
                throw new RuntimeException('Task is not available to claim.');
            }

            if (! $this->taskEligibilityService->isEligible($actor, $task)) {
                throw new RuntimeException('Actor is not eligible to claim this Task.');
            }

            $task->update([
                'assignee_type' => $actor->getMorphClass(),
                'assignee_id' => $actor->getKey(),
                'status' => TaskStatus::Assigned,
                'assigned_at' => now(),
            ]);

            $this->taskEventLogger->log($task->flowInstance, 'task_claimed', $actor, $task);

            return $task->fresh();
        });
    }
}
