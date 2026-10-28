<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rimba\Work\Models\OfferingExecution;

class WorkflowStarted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public OfferingExecution $execution,
        public ?Model $workflowRun = null,
        public bool $queued = false,
    ) {}
}
