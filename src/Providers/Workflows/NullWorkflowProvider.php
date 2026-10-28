<?php

declare(strict_types=1);

namespace Rimba\Work\Providers\Workflows;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Contracts\WorkflowProvider;
use Rimba\Work\Data\WorkflowDefinition;
use Rimba\Work\Enums\WorkflowProviderType;
use Rimba\Work\Models\Workflow;

class NullWorkflowProvider implements WorkflowProvider
{
    public function name(): string
    {
        return WorkflowProviderType::Null->value;
    }

    public function supports(Workflow $workflow): bool
    {
        return $workflow->provider === WorkflowProviderType::Null;
    }

    public function exists(Workflow $workflow): bool
    {
        return true;
    }

    public function resolve(
        Workflow $workflow,
    ): WorkflowDefinition {
        return new WorkflowDefinition(
            provider: $this->name(),
            key: $workflow->code,
            name: $workflow->name,
            active: $workflow->is_active,
        );
    }

    public function start(
        Workflow $workflow,
        ?Model $subject,
        array $payload = [],
        ?bool $queue = null,
    ): ?Model {
        return null;
    }
}
