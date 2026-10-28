<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Rimba\Work\Models\OfferingExecution;
use Throwable;

class OfferingExecutionFailed
{
    use Dispatchable;

    public function __construct(
        public OfferingExecution $execution,
        public Throwable $exception,
    ) {}
}
