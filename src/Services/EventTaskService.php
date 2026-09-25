<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Actions\TriggerEventTask;
use Rimba\Work\Enums\ExecutionType;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Task;

class EventTaskService
{
    public function __construct(
        private TriggerEventTask $triggerEventTask,
    ) {}

    public function trigger(
        string $event,
        array $payload = [],
        ?Model $actor = null,
        ?Model $subject = null,
    ): Collection {
        $query = Task::query()
            ->with('workflowInstance')
            ->where(
                'execution_type',
                ExecutionType::EventDriven,
            )
            ->where(
                'status',
                TaskStatus::Waiting,
            )
            ->where(
                'trigger_event',
                $event,
            );

        if ($subject !== null) {
            $query->whereHas(
                'workflowInstance',
                fn ($workflowQuery) =>
                    $workflowQuery->whereMorphedTo(
                        'subject',
                        $subject,
                    ),
            );
        }

        $tasks = $query->get();
        $triggeredTasks = new Collection();

        foreach ($tasks as $task) {
            $triggeredTasks->push(
                $this->triggerEventTask->execute(
                    $task,
                    $payload,
                    $actor,
                )
            );
        }

        return $triggeredTasks;
    }
}