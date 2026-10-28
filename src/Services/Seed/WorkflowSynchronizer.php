<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Illuminate\Support\Facades\DB;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Models\LifecyclePhase;
use Rimba\Work\Models\Workflow;
use RuntimeException;

class WorkflowSynchronizer
{
    public function sync(
        array $records,
    ): void {
        DB::transaction(function () use ($records): void {
            foreach ($records as $record) {
                $this->syncWorkflow($record);
            }
        });
    }

    protected function syncWorkflow(
        array $record,
    ): Workflow {
        $lifecycleCode = $record['lifecycle_code'] ?? null;

        $phaseCode = $record['lifecycle_phase_code'] ?? null;

        if (
            $lifecycleCode
            && ! Lifecycle::query()
                ->where('code', $lifecycleCode)
                ->exists()
        ) {
            throw new RuntimeException(
                "Lifecycle [{$lifecycleCode}] does not exist."
            );
        }

        if (
            $phaseCode
            && ! LifecyclePhase::query()
                ->where('lifecycle_code', $lifecycleCode)
                ->where('code', $phaseCode)
                ->exists()
        ) {
            throw new RuntimeException(
                "Lifecycle phase [{$lifecycleCode}:{$phaseCode}] does not exist."
            );
        }

        return Workflow::query()->updateOrCreate(
            [
                'code' => $record['code'],
            ],
            [
                'name' => $record['name'],

                'description' => $record['description'] ?? null,

                'lifecycle_code' => $lifecycleCode,

                'lifecycle_phase_code' => $phaseCode,

                'provider' => $record['provider'] ?? 'packstub',

                'provider_key' => $record['provider_key']
                    ?? $record['code'],

                'version' => $record['version'] ?? 1,

                'is_active' => $record['is_active'] ?? true,

                'settings' => $record['settings'] ?? null,
            ],
        );
    }
}
