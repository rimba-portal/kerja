<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum WorkflowInstanceStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
}
