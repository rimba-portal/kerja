<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum FlowInstanceStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Withdrawn = 'withdrawn';
    case Failed = 'failed';

    public function isTerminal(): bool
    {
        return $this !== self::Active;
    }
}
