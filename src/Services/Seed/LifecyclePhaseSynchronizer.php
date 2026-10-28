<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Illuminate\Support\Facades\DB;
use Rimba\Work\Contracts\SeedSynchronizer;
use Rimba\Work\Exceptions\SeedSynchronizationException;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Models\LifecyclePhase;

class LifecyclePhaseSynchronizer implements SeedSynchronizer
{
    public function sync(
        array $records,
    ): void {
        DB::transaction(function () use ($records): void {
            foreach ($records as $record) {
                $this->validate($record);

                $lifecycleExists = Lifecycle::query()
                    ->where(
                        'code',
                        $record['lifecycle_code'],
                    )
                    ->exists();

                if (! $lifecycleExists) {
                    throw new SeedSynchronizationException(
                        "Lifecycle phase [{$record['code']}] references unknown lifecycle [{$record['lifecycle_code']}]."
                    );
                }

                LifecyclePhase::query()->updateOrCreate(
                    [
                        'lifecycle_code' => $record['lifecycle_code'],
                        'code' => $record['code'],
                    ],
                    [
                        'name' => $record['name'],
                        'description' => $record['description'] ?? null,

                        'sequence' => $record['sequence'] ?? 0,

                        'entry_criteria' => $record['entry_criteria'] ?? null,
                        'exit_criteria' => $record['exit_criteria'] ?? null,

                        'settings' => $record['settings'] ?? null,
                    ],
                );
            }
        });
    }

    protected function validate(
        array $record,
    ): void {
        foreach (
            [
                'lifecycle_code',
                'code',
                'name',
            ] as $required
        ) {
            if (! filled($record[$required] ?? null)) {
                throw new SeedSynchronizationException(
                    "Lifecycle phase seed record is missing [{$required}]."
                );
            }
        }
    }
}
