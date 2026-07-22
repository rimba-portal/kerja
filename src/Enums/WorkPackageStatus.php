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

enum WorkPackageStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function isClosed(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Cancelled,
        ], true);
    }
}


