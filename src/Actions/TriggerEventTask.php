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

class TriggerEventTask
{
    public function execute(
        Task $task,
        array $payload = [],
        ?Model $actor = null,
    ): Task {
        return DB::transaction(function () use (
            $task,
            $payload,
            $actor,
        ): Task {
            $task = Task::query()
                ->lockForUpdate()
                ->findOrFail($task->getKey());

            if (
                $task->execution_type
                !== ExecutionType::EventDriven
            ) {
                throw new RuntimeException(
                    "Task [{$task->uuid}] is not event-driven."
                );
            }

            if ($task->status !== TaskStatus::Waiting) {
                throw new RuntimeException(
                    "Event-driven task [{$task->uuid}] cannot be triggered from status [{$task->status->value}]."
                );
            }

            if (
                $task->workflowInstance->status
                === WorkflowStatus::Cancelled
            ) {
                throw new RuntimeException(
                    "Event-driven task [{$task->uuid}] belongs to a cancelled workflow."
                );
            }

            if (
                $task->workflowInstance->status
                === WorkflowStatus::Failed
            ) {
                throw new RuntimeException(
                    "Event-driven task [{$task->uuid}] belongs to a failed workflow."
                );
            }

            $triggeredAt = now();

            $task->update([
                'status' => TaskStatus::Started,
                'payload' => $payload,
                'started_at' => $triggeredAt,
            ]);

            $task->workflowInstance->update([
                'status' => WorkflowStatus::Active,
            ]);

            Transition::query()->create([
                'workflow_instance_id' =>
                    $task->workflow_instance_id,

                'from_task_id' =>
                    $task->getKey(),

                'from_workpackage_slug' =>
                    $task->workpackage_slug,

                'event' =>
                    'event_triggered',

                'actor_type' =>
                    $actor?->getMorphClass(),

                'actor_id' =>
                    $actor?->getKey(),

                'payload' => [
                    'trigger_event' =>
                        $task->trigger_event,

                    'event_payload' =>
                        $payload,
                ],

                'performed_at' =>
                    $triggeredAt,
            ]);

            return app(CompleteTask::class)->execute(
                $task->fresh(),
                [
                    'context' => [
                        'events' => [
                            $task->trigger_event => $payload,
                        ],
                    ],
                ],
                $actor,
            );
        });
    }
}