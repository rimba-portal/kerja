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
        $builder = Task::query()
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

        if ($subject instanceof Model) {
            $builder->whereHas(
                'workflowInstance',
                fn ($workflowQuery) => $workflowQuery->whereMorphedTo(
                    'subject',
                    $subject,
                ),
            );
        }

        $tasks = $builder->get();
        $triggeredTasks = new Collection;

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
