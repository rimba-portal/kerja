<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case Ready = 'ready';
    case Assigned = 'assigned';
    case Started = 'started';
    case Waiting = 'waiting';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
}
