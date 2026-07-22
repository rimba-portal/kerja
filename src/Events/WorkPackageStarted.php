<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\TaskInstance;
use Rimba\Work\Models\WorkPackageInstance;

final class WorkPackageStarted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly WorkPackageInstance $workPackageInstance,
    ) {}
}

