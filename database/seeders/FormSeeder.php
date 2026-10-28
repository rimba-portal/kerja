<?php

declare(strict_types=1);

namespace Rimba\Work\Database\Seeders;

use Illuminate\Database\Seeder;
use Rimba\Work\Services\Seed\FormSynchronizer;
use Rimba\Work\Services\Seed\JsonSeedLoader;
use Rimba\Work\Support\SeedPath;

class FormSeeder extends Seeder
{
    public function run(
        JsonSeedLoader $loader,
        FormSynchronizer $synchronizer,
    ): void {
        $synchronizer->sync(
            $loader->load(
                SeedPath::make('forms.json'),
            ),
        );
    }
}
