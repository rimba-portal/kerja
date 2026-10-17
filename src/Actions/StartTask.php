<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Services\TaskEventLogger;
use RuntimeException;

final class StartTask
{
    public function __construct(private TaskEventLogger $taskEventLogger) {}

    public function execute(Task $task, Model $actor): Task
    {
        return DB::transaction(function () use ($task, $actor): Task {
            $task = Task::query()->lockForUpdate()->findOrFail($task->getKey());

            if ($task->status !== TaskStatus::Assigned) {
                throw new RuntimeException('Only an assigned Task may be started.');
            }

            if (! $task->assignee?->is($actor)) {
                throw new RuntimeException('Only the Task assignee may start this Task.');
            }

            $task->update([
                'status' => TaskStatus::Started,
                'started_at' => now(),
            ]);

            $this->taskEventLogger->log($task->flowInstance, 'task_started', $actor, $task);

            return $task->fresh();
        });
    }
}
