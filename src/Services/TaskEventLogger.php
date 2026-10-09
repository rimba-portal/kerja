<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\TaskEvent;
use Rimba\Work\Models\WorkFlowAction;
use Rimba\Work\Models\WorkFlowInstance;

final class TaskEventLogger
{
    public function log(WorkFlowInstance $instance, string $event, ?Model $actor = null, ?Task $task = null, ?WorkFlowAction $action = null, array $payload = []): TaskEvent
    {
        return TaskEvent::query()->create([
            'workflow_instance_id' => $instance->id, 'task_id' => $task?->id, 'workflow_action_id' => $action?->id,
            'actor_type' => $actor?->getMorphClass(), 'actor_id' => $actor?->getKey(), 'event' => $event,
            'payload' => $payload ?: null, 'occurred_at' => now(),
        ]);
    }
}
