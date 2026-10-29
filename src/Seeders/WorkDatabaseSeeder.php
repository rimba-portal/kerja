<?php

declare(strict_types=1);

namespace Rimba\Work\Seeders;

use Illuminate\Database\Seeder;
use Rimba\Work\Services\Seed\SeedBundleDiscovery;
use Rimba\Work\Services\Seed\SeedBundleSynchronizer;
use Rimba\Work\Support\SeedPath;

final class WorkDatabaseSeeder extends Seeder
{
    public function run(
        SeedBundleDiscovery $discovery,
        SeedBundleSynchronizer $synchronizer,
    ): void {
        foreach (
            $discovery->discover(SeedPath::directory()) as $file
        ) {
            $synchronizer->syncFile($file);

            $this->command?->info(
                'Synchronized: '.basename($file)
            );
        }
    }
}
