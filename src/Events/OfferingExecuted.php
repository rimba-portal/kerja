<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rimba\Work\Data\ExecutionResult;

class OfferingExecuted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public ExecutionResult $result,
    ) {}
}
