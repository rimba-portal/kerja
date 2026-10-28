<?php

declare(strict_types=1);

namespace Rimba\Work\Database\Seeders;

use Illuminate\Database\Seeder;
use Rimba\Work\Services\Seed\JsonSeedLoader;
use Rimba\Work\Services\Seed\LifecyclePhaseSynchronizer;
use Rimba\Work\Support\SeedPath;

class LifecyclePhaseSeeder extends Seeder
{
    public function run(
        JsonSeedLoader $loader,
        LifecyclePhaseSynchronizer $synchronizer,
    ): void {
        $synchronizer->sync(
            $loader->load(
                SeedPath::make('lifecycle-phases.json'),
            ),
        );
    }
}
