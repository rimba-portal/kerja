<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;
use Rimba\Work\Enums\ActionType;
use Rimba\Work\Enums\FlowInstanceStatus;
use Rimba\Work\Enums\StepType;
use Rimba\Work\Traits\HasHookBindings;

#[Table(name: 'work_flow_actions')]
#[Unguarded]
class FlowAction extends Model
{
    use HasHookBindings;

    protected function casts(): array
    {
        return [
            'action_type' => ActionType::class,
            'terminal_status' => FlowInstanceStatus::class,
            'requires_confirmation' => 'boolean',
            'sequence' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $action): void {
            $hasTarget = $action->target_workflow_step_id !== null;
            $hasTerminalStatus = $action->terminal_status !== null;

            if ($hasTarget === $hasTerminalStatus) {
                throw new InvalidArgumentException(
                    'A FlowAction must define exactly one of target_workflow_step_id or terminal_status.'
                );
            }

            if ($action->terminal_status === FlowInstanceStatus::Active) {
                throw new InvalidArgumentException(
                    'Active cannot be used as a terminal FlowAction status.'
                );
            }

            $step = $action->flowStep;

            if ($step?->step_type instanceof StepType
                && ! $step->step_type->allows($action->action_type)) {
                throw new InvalidArgumentException(sprintf(
                    'ActionType [%s] is not allowed for StepType [%s].',
                    $action->action_type->value,
                    $step->step_type->value,
                ));
            }
        });
    }

    public function flowStep(): BelongsTo
    {
        return $this->belongsTo(FlowStep::class, 'workflow_step_id');
    }

    public function targetFlowStep(): BelongsTo
    {
        return $this->belongsTo(
            FlowStep::class,
            'target_workflow_step_id'
        );
    }

    public function isTerminal(): bool
    {
        return $this->terminal_status !== null;
    }
}
