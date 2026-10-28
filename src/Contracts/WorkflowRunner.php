<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\Workflow;

interface WorkflowRunner
{
    public function run(
        Workflow $workflow,
        ?Model $subject,
        array $payload = [],
        ?bool $queue = null,
    ): ?Model;
}
