<?php

declare(strict_types=1);

namespace Rimba\Work\Database\Seeders;

use Illuminate\Database\Seeder;
use Rimba\Work\Services\Seed\JsonSeedLoader;
use Rimba\Work\Services\Seed\WorkflowSynchronizer;
use Rimba\Work\Support\SeedPath;

class WorkflowSeeder extends Seeder
{
    public function run(
        JsonSeedLoader $loader,
        WorkflowSynchronizer $synchronizer,
    ): void {
        $synchronizer->sync(
            $loader->load(
                SeedPath::make('workflows.json'),
            ),
        );
    }
}
