<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum WorkflowStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Waiting = 'waiting';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
}
