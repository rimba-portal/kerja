<?php

declare(strict_types=1);

namespace Rimba\Work\Registries;

use Rimba\Work\Contracts\WorkflowProvider;
use Rimba\Work\Exceptions\WorkflowProviderException;
use Rimba\Work\Models\Workflow;

class WorkflowProviderRegistry
{
    /**
     * @var array<string, WorkflowProvider>
     */
    protected array $providers = [];

    public function register(
        WorkflowProvider $provider,
    ): static {
        $this->providers[$provider->name()] = $provider;

        return $this;
    }

    public function get(string $name): WorkflowProvider
    {
        return $this->providers[$name]
            ?? throw new WorkflowProviderException(
                "Workflow provider [{$name}] is not registered."
            );
    }

    public function for(
        Workflow $workflow,
    ): WorkflowProvider {
        $workflowProvider = $this->get(
            $workflow->provider->value,
        );

        if (! $workflowProvider->supports($workflow)) {
            throw new WorkflowProviderException(
                "Provider [{$workflowProvider->name()}] does not support workflow [{$workflow->code}]."
            );
        }

        return $workflowProvider;
    }

    /**
     * @return array<string, WorkflowProvider>
     */
    public function all(): array
    {
        return $this->providers;
    }
}
