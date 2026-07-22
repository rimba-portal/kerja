<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rimba\Work\Models\ChecklistInstance;

final class ChecklistCompleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly ChecklistInstance $checklistInstance,
    ) {}
}
