<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rimba\Work\Models\TaskInstance;

final class TaskCompleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly TaskInstance $taskInstance,
    ) {}
}
