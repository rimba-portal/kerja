<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Rimba\Work\Enums\HookOperation;
use Rimba\Work\Models\Endpoint;
use Rimba\Work\Models\HookBinding;
use Rimba\Work\Support\HookContext;
use RuntimeException;

final class HookRunner
{
    public function __construct(
        private HookPath $hookPath,
        private ConverterRegistry $converterRegistry,
        private EndpointClient $endpointClient,
    ) {}

    public function run(
        HookBinding $binding,
        HookContext $context,
    ): void {
        if (! $binding->is_enabled) {
            return;
        }

        $hook = $binding->hook;

        if (! $hook->is_active) {
            if ($binding->is_required) {
                throw new RuntimeException(
                    "Required Hook [{$hook->code}] is inactive."
                );
            }

            logger()->warning('Optional Kerja Hook is inactive.', [
                'hook' => $hook->code,
                'binding_id' => $binding->id,
            ]);

            return;
        }

        $context = new HookContext(
            instance: $context->instance,
            task: $context->task,
            step: $context->step,
            action: $context->action,
            actor: $context->actor,
            parameters: $binding->parameters ?? [],
        );

        try {
            match ($hook->operation) {
                HookOperation::Get => $this->runGet($binding, $context),

                HookOperation::Transform => $this->runTransform($binding, $context),

                HookOperation::Put => $this->runPut($binding, $context),
            };
        } catch (\Throwable $throwable) {
            if ($binding->is_required) {
                throw $throwable;
            }

            logger()->warning('Optional Kerja Hook failed.', [
                'hook' => $hook->code,
                'binding_id' => $binding->id,
                'message' => $throwable->getMessage(),
            ]);
        }
    }

    private function runGet(
        HookBinding $binding,
        HookContext $context,
    ): void {
        $configuration = $this->configuration($binding);

        $response = $this->endpointClient->request(
            $this->endpoint($configuration),
            (string) ($configuration['method'] ?? 'GET'),
            $this->hookPath->interpolate(
                (string) ($configuration['path'] ?? ''),
                $context,
            ),
            $this->requestData($configuration, $context),
        );

        foreach ($configuration['mappings'] ?? [] as $mapping) {
            $value = $this->hookPath->read(
                (string) $mapping['from'],
                $context,
                [],
                $response,
            );

            $value = $this->converterRegistry->convert(
                $mapping['converter'] ?? null,
                $value,
                $this->resolveArguments(
                    $mapping['arguments'] ?? [],
                    $context,
                ),
            );

            $this->hookPath->writeInternal(
                (string) $mapping['to'],
                $value,
                $context,
            );
        }

        $context->instance->save();
    }

    private function runTransform(
        HookBinding $binding,
        HookContext $context,
    ): void {
        $configuration = $this->configuration($binding);

        foreach ($configuration['mappings'] ?? [] as $mapping) {
            $value = $this->hookPath->read(
                (string) $mapping['from'],
                $context,
            );

            $value = $this->converterRegistry->convert(
                $mapping['converter'] ?? null,
                $value,
                $this->resolveArguments(
                    $mapping['arguments'] ?? [],
                    $context,
                ),
            );

            $this->hookPath->writeInternal(
                (string) $mapping['to'],
                $value,
                $context,
            );
        }

        $context->instance->save();
    }

    private function runPut(
        HookBinding $binding,
        HookContext $context,
    ): void {
        $configuration = $this->configuration($binding);
        $request = [];

        foreach ($configuration['mappings'] ?? [] as $mapping) {
            $value = $this->hookPath->read(
                (string) $mapping['from'],
                $context,
            );

            $value = $this->converterRegistry->convert(
                $mapping['converter'] ?? null,
                $value,
                $this->resolveArguments(
                    $mapping['arguments'] ?? [],
                    $context,
                ),
            );

            $this->hookPath->writeRequest(
                $request,
                (string) $mapping['to'],
                $value,
            );
        }

        $response = $this->endpointClient->request(
            $this->endpoint($configuration),
            (string) ($configuration['method'] ?? 'PUT'),
            $this->hookPath->interpolate(
                (string) ($configuration['path'] ?? ''),
                $context,
            ),
            $request,
        );

        foreach ($configuration['response_mappings'] ?? [] as $mapping) {
            $value = $this->hookPath->read(
                (string) $mapping['from'],
                $context,
                $request,
                $response,
            );

            $this->hookPath->writeInternal(
                (string) $mapping['to'],
                $value,
                $context,
            );
        }

        $context->instance->save();
    }

    private function configuration(HookBinding $binding): array
    {
        return $binding->hook->configuration ?? [];
    }

    private function endpoint(array $configuration): Endpoint
    {
        $code = $configuration['endpoint'] ?? null;

        if (! is_string($code) || blank($code)) {
            throw new RuntimeException(
                'Hook Endpoint code is required.'
            );
        }

        return Endpoint::query()
            ->where('code', $code)
            ->firstOrFail();
    }

    private function requestData(
        array $configuration,
        HookContext $context,
    ): array {
        $request = [];

        foreach ($configuration['request_mappings'] ?? [] as $mapping) {
            $value = $this->hookPath->read(
                (string) $mapping['from'],
                $context,
            );

            $this->hookPath->writeRequest(
                $request,
                (string) $mapping['to'],
                $value,
            );
        }

        return $request;
    }

    private function resolveArguments(
        array $arguments,
        HookContext $context,
    ): array {
        if (isset($arguments['value_from'])) {
            $arguments['value'] = $this->hookPath->read(
                (string) $arguments['value_from'],
                $context,
            );
        }

        return $arguments;
    }
}
