<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Illuminate\Support\Facades\DB;
use Rimba\Work\Models\Form;

class FormSynchronizer
{
    public function sync(
        array $records,
    ): void {
        DB::transaction(function () use ($records): void {
            foreach ($records as $record) {
                $this->syncForm($record);
            }
        });
    }

    protected function syncForm(
        array $record,
    ): Form {
        return Form::query()->updateOrCreate(
            [
                'code' => $record['code'],
            ],
            [
                'name' => $record['name'],

                'description' => $record['description'] ?? null,

                'provider' => $record['provider'] ?? 'packstub',

                'provider_key' => $record['provider_key']
                    ?? $record['code'],

                'display_mode' => $record['display_mode']
                    ?? 'single',

                'schema' => $record['schema'] ?? null,

                'settings' => $record['settings'] ?? null,

                'version' => $record['version'] ?? 1,

                'is_active' => $record['is_active'] ?? true,
            ],
        );
    }
}
