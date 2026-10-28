<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Contracts\WorkflowProvider;
use Rimba\Work\Contracts\WorkflowRunner;
use Rimba\Work\Data\WorkflowDefinition;
use Rimba\Work\Models\Workflow;
use Rimba\Work\Registries\WorkflowProviderRegistry;

class WorkflowManager implements WorkflowRunner
{
    public function __construct(
        protected WorkflowProviderRegistry $providers,
    ) {}

    public function provider(
        Workflow $workflow,
    ): WorkflowProvider {
        return $this->providers->for($workflow);
    }

    public function resolve(
        Workflow $workflow,
    ): WorkflowDefinition {
        return $this
            ->provider($workflow)
            ->resolve($workflow);
    }

    public function run(
        Workflow $workflow,
        ?Model $subject,
        array $payload = [],
        ?bool $queue = null,
    ): ?Model {
        return $this
            ->provider($workflow)
            ->start(
                workflow: $workflow,
                subject: $subject,
                payload: $payload,
                queue: $queue,
            );
    }
}
