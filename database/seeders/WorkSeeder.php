<?php

declare(strict_types=1);

namespace Rimba\Work\Database\Seeders;

use Illuminate\Database\Seeder;
use Rimba\Work\Models\ActivityType;

final class WorkSeeder extends Seeder
{
    public function run(): void
    {
        foreach (
            [
                ['creates', 'Create'],
                ['captures', 'Capture'],
                ['verifies', 'Verify'],
                ['analyzes', 'Analyze'],
                ['decides', 'Decide'],
                ['authorizes', 'Authorize'],
                ['transforms', 'Transform'],
                ['executes', 'Execute'],
                ['records', 'Record'],
                ['informs', 'Communicate'],
            ] as [$code, $name]
        ) {
            ActivityType::query()->updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
