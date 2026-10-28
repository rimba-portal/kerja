<?php

declare(strict_types=1);

namespace Rimba\Work\Database\Seeders;

use Illuminate\Database\Seeder;
use Rimba\Work\Services\Seed\JsonSeedLoader;
use Rimba\Work\Services\Seed\OfferingSynchronizer;
use Rimba\Work\Support\SeedPath;

class OfferingSeeder extends Seeder
{
    public function run(
        JsonSeedLoader $loader,
        OfferingSynchronizer $synchronizer,
    ): void {
        $synchronizer->sync(
            $loader->load(
                SeedPath::make('offerings.json'),
            ),
        );
    }
}
