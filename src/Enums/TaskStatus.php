<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| ENUMS
|--------------------------------------------------------------------------
|
| Centralized status values.
| Avoid hard-coded strings throughout the package.
|
*/

namespace Rimba\Work\Enums;

enum TaskStatus: string
{
    case Queue = 'queue';
    case Active = 'active';
    case Completed = 'completed';
    case Skipped = 'skipped';
    case Cancelled = 'cancelled';

    public function isOpen(): bool
    {
        return in_array($this, [
            self::Queue,
            self::Active,
        ], true);
    }

    public function isClosed(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Skipped,
            self::Cancelled,
        ], true);
    }
}

