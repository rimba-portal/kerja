<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rimba\Work\Models\OfferingExecution;

class OfferingExecutionStarted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public OfferingExecution $execution,
    ) {}
}
