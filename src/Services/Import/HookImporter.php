<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Import;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use JsonException;
use Rimba\Work\Models\Flow;
use Rimba\Work\Models\FlowAction;
use Rimba\Work\Models\FlowStep;

final class HookImporter
{
    public function __construct(
        private ReferenceResolver $referenceResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, int>
     *
     * @throws JsonException
     */
    public function import(array $payload): array
    {
        return [
            'endpoints' => $this->importEndpoints(
                $payload['endpoints'] ?? [],
            ),
            'hooks' => $this->importHooks(
                $payload['hooks'] ?? [],
            ),
            'hook_bindings' => $this->importHookBindings(
                $payload['hook_bindings'] ?? [],
            ),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importEndpoints(array $items): int
    {
        foreach ($items as $item) {
            $code = $this->requiredCode($item, 'endpoint');

            DB::table('work_endpoints')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'base_url' => $item['base_url'],
                    'auth_profile' => $item['auth_profile'] ?? null,
                    'timeout_seconds' => $item['timeout_seconds'] ?? 30,
                    'is_active' => $item['is_active'] ?? true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }

        return count($items);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     *
     * @throws JsonException
     */
    private function importHooks(array $items): int
    {
        foreach ($items as $item) {
            $code = $this->requiredCode($item, 'hook');

            DB::table('work_hooks')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'operation' => $item['operation'],
                    'version' => $item['version'] ?? 1,
                    'schema' => json_encode(
                        $item['schema'] ?? [],
                        JSON_THROW_ON_ERROR,
                    ),
                    'configuration' => json_encode(
                        $item['configuration'] ?? [],
                        JSON_THROW_ON_ERROR,
                    ),
                    'is_active' => $item['is_active'] ?? true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }

        return count($items);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     *
     * @throws JsonException
     */
    private function importHookBindings(array $items): int
    {
        foreach ($items as $item) {
            $hookCode = $item['hook'] ?? null;

            if (! is_string($hookCode) || blank($hookCode)) {
                throw new InvalidArgumentException(
                    'HookBinding requires a Hook code.'
                );
            }

            [$hookableType, $hookableId] =
                $this->resolveHookable($item);

            DB::table('work_hook_bindings')->updateOrInsert(
                [
                    'hook_id' => $this->referenceResolver->id(
                        'work_hooks',
                        $hookCode,
                        'hook',
                    ),
                    'hookable_type' => $hookableType,
                    'hookable_id' => $hookableId,
                    'hook_point' => $item['hook_point'],
                ],
                [
                    'sequence' => $item['sequence'] ?? 1,
                    'parameters' => isset($item['parameters'])
                        ? json_encode(
                            $item['parameters'],
                            JSON_THROW_ON_ERROR,
                        )
                        : null,
                    'is_required' => $item['is_required'] ?? true,
                    'is_enabled' => $item['is_enabled'] ?? true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }

        return count($items);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{0: string, 1: int}
     */
    private function resolveHookable(array $item): array
    {
        $type = $item['hookable_type'] ?? null;

        return match ($type) {
            'flow', 'workflow' => [
                (new Flow)->getMorphClass(),
                $this->referenceResolver->flowId(
                    (string) $item['workflow'],
                ),
            ],

            'step' => [
                (new FlowStep)->getMorphClass(),
                $this->referenceResolver->stepId(
                    $this->referenceResolver->flowId(
                        (string) $item['workflow'],
                    ),
                    (string) $item['step'],
                ),
            ],

            'action' => [
                (new FlowAction)->getMorphClass(),
                $this->referenceResolver->actionId(
                    (string) $item['workflow'],
                    (string) $item['step'],
                    (string) $item['action_type'],
                ),
            ],

            default => throw new InvalidArgumentException(
                "Unsupported hookable_type [{$type}]."
            ),
        };
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function requiredCode(
        array $item,
        string $type,
    ): string {
        $code = $item['code'] ?? null;

        if (! is_string($code) || blank($code)) {
            throw new InvalidArgumentException(
                ucfirst($type).' requires a non-empty [code].'
            );
        }

        return $code;
    }
}
