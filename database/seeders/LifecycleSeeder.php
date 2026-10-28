<?php

declare(strict_types=1);

namespace Rimba\Work\Database\Seeders;

use Illuminate\Database\Seeder;
use Rimba\Work\Services\Seed\JsonSeedLoader;
use Rimba\Work\Services\Seed\LifecycleSynchronizer;
use Rimba\Work\Support\SeedPath;

class LifecycleSeeder extends Seeder
{
    public function run(
        JsonSeedLoader $loader,
        LifecycleSynchronizer $synchronizer,
    ): void {
        $synchronizer->sync(
            $loader->load(
                SeedPath::make('lifecycles.json'),
            ),
        );
    }
}
