<?php

declare(strict_types=1);

namespace Rimba\Work\Database\Seeders;

use Illuminate\Database\Seeder;

class WorkDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LifecycleSeeder::class,
            LifecyclePhaseSeeder::class,

            FormSeeder::class,
            WorkflowSeeder::class,

            OfferingSeeder::class,
        ]);
    }
}
