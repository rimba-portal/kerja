<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Illuminate\Support\Facades\DB;
use Rimba\Work\Contracts\SeedSynchronizer;
use Rimba\Work\Exceptions\SeedSynchronizationException;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Models\LifecyclePhase;
use Rimba\Work\Models\Workflow;

class WorkflowSynchronizer implements SeedSynchronizer
{
    public function sync(
        array $records,
    ): void {
        DB::transaction(function () use ($records): void {
            foreach ($records as $record) {
                $this->syncWorkflow(
                    $record,
                );
            }
        });
    }

    protected function syncWorkflow(
        array $record,
    ): Workflow {
        $code = $record['code'];

        $orgUnitCode =
            $record['org_unit_code']
            ?? throw new SeedSynchronizationException(
                "Workflow [{$code}] is missing org_unit_code."
            );

        $lifecycleCode =
            $record['lifecycle_code']
            ?? null;

        $phaseCode =
            $record['lifecycle_phase_code']
            ?? null;

        $this->validateLifecycle(
            $lifecycleCode,
        );

        $this->validatePhase(
            lifecycleCode: $lifecycleCode,
            phaseCode: $phaseCode,
        );

        return Workflow::query()->updateOrCreate(
            [
                'code' => $code,
            ],
            [
                'org_unit_id' => $this->resolveOrgUnitId(
                    $orgUnitCode,
                ),

                'name' => $record['name'],

                'description' => $record['description']
                    ?? null,

                'lifecycle_code' => $lifecycleCode,

                'lifecycle_phase_code' => $phaseCode,

                'provider' => $record['provider']
                    ?? 'packstub',

                /*
                 * Portable provider identity.
                 *
                 * Do not put Packstub's environment-specific
                 * database ID into seed JSON.
                 */
                'provider_key' => $record['provider_key']
                    ?? $code,

                'version' => $record['version']
                    ?? 1,

                'is_active' => $record['is_active']
                    ?? true,

                'settings' => $record['settings']
                    ?? null,
            ],
        );
    }

    protected function validateLifecycle(
        ?string $code,
    ): void {
        if ($code === null) {
            return;
        }

        if (
            ! Lifecycle::query()
                ->where('code', $code)
                ->exists()
        ) {
            throw new SeedSynchronizationException(
                "Lifecycle [{$code}] does not exist."
            );
        }
    }

    protected function validatePhase(
        ?string $lifecycleCode,
        ?string $phaseCode,
    ): void {
        if ($phaseCode === null) {
            return;
        }

        if ($lifecycleCode === null) {
            throw new SeedSynchronizationException(
                "Lifecycle phase [{$phaseCode}] cannot be used without lifecycle_code."
            );
        }

        if (
            ! LifecyclePhase::query()
                ->where(
                    'lifecycle_code',
                    $lifecycleCode,
                )
                ->where(
                    'code',
                    $phaseCode,
                )
                ->exists()
        ) {
            throw new SeedSynchronizationException(
                "Lifecycle phase [{$lifecycleCode}:{$phaseCode}] does not exist."
            );
        }
    }

    protected function resolveOrgUnitId(
        string $code,
    ): int|string {
        $model = config(
            'work.models.org_unit',
        );

        if (! $model) {
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
