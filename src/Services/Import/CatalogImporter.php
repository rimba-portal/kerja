<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Import;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class CatalogImporter
{
    public function __construct(
        private ReferenceResolver $referenceResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, int>
     */
    public function import(array $payload): array
    {
        return [
            'activity_types' => $this->importItems(
                'work_activity_types',
                $payload['activity_types'] ?? [],
            ),
            'business_objects' => $this->importItems(
                'work_business_objects',
                $payload['business_objects'] ?? [],
            ),
            'lifecycles' => $this->importItems(
                'work_lifecycles',
                $payload['lifecycles'] ?? [],
            ),
            'lifecycle_phases' => $this->importItems(
                'work_lifecycle_phases',
                $payload['lifecycle_phases'] ?? [],
            ),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importItems(
        string $table,
        array $items,
    ): int {
        foreach ($items as $item) {
            $code = $this->requiredCode($item, $table);
            $row = $item;

            unset($row['code']);

            if (isset($row['business_object'])) {
                $row['business_object_id'] = $this->referenceResolver->id(
                    'work_business_objects',
                    (string) $row['business_object'],
                    'business object',
                );

                unset($row['business_object']);
            }

            if (isset($row['lifecycle'])) {
                $row['lifecycle_id'] = $this->referenceResolver->id(
                    'work_lifecycles',
                    (string) $row['lifecycle'],
                    'lifecycle',
                );

                unset($row['lifecycle']);
            }

            DB::table($table)->updateOrInsert(
                ['code' => $code],
                $row + [
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }

        return count($items);
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
