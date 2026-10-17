<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

use Filament\Support\Contracts\HasLabel;

enum HookPoint: string implements HasLabel
{
    case WorkflowLoad = 'workflow_load';
    case StepEnter = 'step_enter';
    case StepExit = 'step_exit';
    case ActionExit = 'action_exit';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::WorkflowLoad => 'Workflow Load',
            self::StepEnter => 'Step Enter',
            self::StepExit => 'Step Exit',
            self::ActionExit => 'Action Exit',
        };
    }
}
