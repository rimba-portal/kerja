<?php

declare(strict_types=1);

namespace Rimba\Work\Providers\Workflows;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Packstub\Flow\Facades\Flow;
use Rimba\Work\Contracts\WorkflowProvider;
use Rimba\Work\Data\WorkflowDefinition;
use Rimba\Work\Enums\WorkflowProviderType;
use Rimba\Work\Exceptions\WorkflowProviderException;
use Rimba\Work\Exceptions\WorkflowStartException;
use Rimba\Work\Models\Workflow;
use Throwable;

class PackstubWorkflowProvider implements WorkflowProvider
{
    public function name(): string
    {
        return WorkflowProviderType::Packstub->value;
    }

    public function supports(Workflow $workflow): bool
    {
        return $workflow->provider === WorkflowProviderType::Packstub;
    }

    public function designerUrl(
        Workflow $workflow,
    ): ?string {
        return null;
    }

    public function exists(Workflow $workflow): bool
    {
        return $this->query($workflow)->exists();
    }

    public function resolve(
        Workflow $workflow,
    ): WorkflowDefinition {
        $providerWorkflow = $this
            ->query($workflow)
            ->first();

        if (! $providerWorkflow) {
            throw new WorkflowProviderException(
                "Packstub workflow [{$workflow->provider_key}] was not found."
            );
        }

        return new WorkflowDefinition(
            provider: $this->name(),
            key: (string) $workflow->provider_key,
            name: (string) (
                data_get($providerWorkflow, 'name')
                ?? $workflow->name
            ),
            active: (bool) data_get(
                $providerWorkflow,
                'is_active',
                true,
            ),
            definition: (array) data_get(
                $providerWorkflow,
                'definition',
                [],
            ),
            providerModel: $providerWorkflow,
        );
    }

    public function start(
        Workflow $workflow,
        ?Model $subject,
        array $payload = [],
        ?bool $queue = null,
    ): ?Model {
        $workflowDefinition = $this->resolve($workflow);

        if (! $workflowDefinition->active) {
            throw new WorkflowStartException(
                "Packstub workflow [{$workflow->provider_key}] is inactive."
            );
        }

        $queue ??= config(
            'work.providers.workflows.drivers.packstub.queue',
        );

        $payload = [
            ...$payload,
            'model' => $subject,
        ];

        try {
            return Flow::run(
                $workflowDefinition->providerModel,
                $payload,
                queue: $queue,
            );
        } catch (Throwable $throwable) {
            throw new WorkflowStartException(message: "Unable to start Packstub workflow [{$workflow->provider_key}].", code: $throwable->getCode(), previous: $throwable);
        }
    }

    protected function query(
        Workflow $workflow,
    ): Builder {
        $modelClass = $this->workflowModelClass();

        $lookupColumn = config(
            'work.providers.workflows.drivers.packstub.lookup_column',
            'id',
        );

        return $modelClass::query()->where(
            $lookupColumn,
            $workflow->provider_key,
        );
    }

    /**
     * @return class-string<Model>
     */
    protected function workflowModelClass(): string
    {
        $modelClass = config(
            'work.providers.workflows.drivers.packstub.workflow_model',
            \Packstub\Flow\Models\Workflow::class,
        );

        if (! is_a($modelClass, Model::class, true)) {
            throw new WorkflowProviderException(
                'The configured Packstub workflow model is invalid.'
            );
        }

        return $modelClass;
    }
}
