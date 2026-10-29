<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Rimba\Work\Contracts\SeedSynchronizer;
use Rimba\Work\Exceptions\SeedSynchronizationException;
use Rimba\Work\Models\Workflow;

final class WorkflowSynchronizer implements SeedSynchronizer
{
    public function sync(array $records): void
    {
        foreach ($records as $record) {
            $this->syncRecord($record);
        }
    }

    private function syncRecord(array $record): Workflow
    {
        $code = $this->requiredString(
            $record,
            'code',
        );

        $orgUnitCode = $this->requiredString(
            $record,
            'org_unit_code',
        );

        return Workflow::query()->updateOrCreate(
            [
                'code' => $code,
            ],
            [
                'org_unit_id' => $this->resolveOrgUnitId(
                    $orgUnitCode,
                ),

                'name' => $this->requiredString(
                    $record,
                    'name',
                ),

                'description' => $record['description'] ?? null,

                /*
                 * Semantic references owned by Rimba/Jalur.
                 *
                 * Kerja stores the codes only and deliberately
                 * does not enforce database foreign keys.
                 */
                'lifecycle_code' => $record['lifecycle_code'] ?? null,

                'lifecycle_phase_code' => $record['lifecycle_phase_code'] ?? null,

                'provider' => $record['provider'] ?? 'packstub',

                'provider_key' => $record['provider_key'] ?? $code,

                'provider_reference' => $record['provider_reference'] ?? null,

                'version' => $record['version'] ?? 1,

                'is_active' => $record['is_active'] ?? true,

                'settings' => $record['settings'] ?? null,
            ],
        );
    }

    private function requiredString(
        array $record,
        string $key,
    ): string {
        $value = $record[$key] ?? null;

        if (
            ! is_string($value)
            || trim($value) === ''
        ) {
            $code = $record['code']
                ?? '(unknown)';

            throw new SeedSynchronizationException(
                "Workflow [{$code}] requires [{$key}]."
            );
        }

        return trim($value);
    }

    private function resolveOrgUnitId(
        string $code,
    ): int|string {
        $model = config(
            'work.models.org_unit',
        );

        if (
            ! is_string($model)
            || $model === ''
        ) {
            throw new SeedSynchronizationException(
                'work.models.org_unit is not configured.'
            );
        }

        $unit = $model::query()
            ->where('code', $code)
            ->first();

        if (! $unit) {
            throw new SeedSynchronizationException(
                "OrgUnit [{$code}] does not exist."
            );
        }

        return $unit->getKey();
    }
}
