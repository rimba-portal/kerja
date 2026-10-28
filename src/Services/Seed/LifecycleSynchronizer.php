<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Illuminate\Support\Facades\DB;
use Rimba\Work\Contracts\SeedSynchronizer;
use Rimba\Work\Exceptions\SeedSynchronizationException;
use Rimba\Work\Models\Lifecycle;

class LifecycleSynchronizer implements SeedSynchronizer
{
    public function sync(
        array $records,
    ): void {
        DB::transaction(function () use ($records): void {
            /*
             * Pass 1:
             * Ensure every lifecycle exists.
             */
            foreach ($records as $record) {
                $this->validate($record);

                Lifecycle::query()->updateOrCreate(
                    [
                        'code' => $record['code'],
                    ],
                    [
                        'name' => $record['name'],
                        'description' => $record['description'] ?? null,
                        'category' => $record['category'],
                        'sort' => $record['sort'] ?? 0,
                        'is_active' => $record['is_active'] ?? true,
                        'settings' => $record['settings'] ?? null,

                        /*
                         * Parent is synchronized in pass 2.
                         */
                        'parent_code' => null,
                    ],
                );
            }

            /*
             * Pass 2:
             * Resolve lifecycle hierarchy.
             */
            foreach ($records as $record) {
                $parentCode = $record['parent_code'] ?? null;

                if ($parentCode !== null) {
                    $exists = Lifecycle::query()
                        ->where('code', $parentCode)
                        ->exists();

                    if (! $exists) {
                        throw new SeedSynchronizationException(
                            "Lifecycle [{$record['code']}] references unknown parent [{$parentCode}]."
                        );
                    }
                }

                Lifecycle::query()
                    ->where('code', $record['code'])
                    ->update([
                        'parent_code' => $parentCode,
                    ]);
            }
        });
    }

    protected function validate(
        array $record,
    ): void {
        foreach (
            [
                'code',
                'name',
                'category',
            ] as $required
        ) {
            if (! filled($record[$required] ?? null)) {
                throw new SeedSynchronizationException(
                    "Lifecycle seed record is missing [{$required}]."
                );
            }
        }

        if (
            isset($record['parent_code'])
            && $record['parent_code'] === $record['code']
        ) {
            throw new SeedSynchronizationException(
                "Lifecycle [{$record['code']}] cannot be its own parent."
            );
        }
    }
}
