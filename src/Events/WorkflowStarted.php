<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Rimba\Work\Models\WorkflowInstance;

final readonly class WorkflowStarted
{
    public function __construct(public WorkflowInstance $workflowInstance) {}
}
