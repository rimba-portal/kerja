<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Data\WorkflowDefinition;
use Rimba\Work\Models\Workflow;

interface WorkflowProvider
{
    public function name(): string;

    public function supports(Workflow $workflow): bool;

    public function exists(Workflow $workflow): bool;

    public function resolve(
        Workflow $workflow,
    ): WorkflowDefinition;

    public function start(
        Workflow $workflow,
        ?Model $subject,
        array $payload = [],
        ?bool $queue = null,
    ): ?Model;

    public function designerUrl(
        Workflow $workflow,
    ): ?string;
}
