<?php

declare(strict_types=1);

namespace Rimba\Work\Support;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\FlowAction;
use Rimba\Work\Models\FlowInstance;
use Rimba\Work\Models\FlowStep;
use Rimba\Work\Models\Task;

final readonly class HookContext
{
    public function __construct(
        public FlowInstance $instance,
        public ?Task $task = null,
        public ?FlowStep $step = null,
        public ?FlowAction $action = null,
        public ?Model $actor = null,
        public array $parameters = [],
    ) {}

    public function values(): array
    {
        return [
            'workflow_instance' => [
                'id' => $this->instance->getKey(),
                'uuid' => $this->instance->uuid,
            ],
            'task' => [
                'id' => $this->task?->getKey(),
                'uuid' => $this->task?->uuid,
            ],
            'requestor' => [
                'id' => $this->instance->initiator?->getKey(),
                'type' => $this->instance->initiator_type,
            ],
            'actor' => [
                'id' => $this->actor?->getKey(),
                'type' => $this->actor?->getMorphClass(),
            ],
            'subject' => [
                'id' => $this->instance->subject?->getKey(),
                'type' => $this->instance->subject_type,
            ],
            'step' => [
                'id' => $this->step?->getKey(),
                'code' => $this->step?->code,
            ],
            'action' => [
                'id' => $this->action?->getKey(),
                'type' => $this->action?->action_type?->value,
            ],
        ];
    }
}
