<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum ExecutionStatus: string
{
    case Pending = 'pending';
    case Preparing = 'preparing';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
}
