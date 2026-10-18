<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Rimba\Work\Models\FlowInstance;

final readonly class FlowStarted
{
    public function __construct(
        public FlowInstance $flowInstance,
    ) {}
}
