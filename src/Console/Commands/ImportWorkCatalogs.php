<?php

declare(strict_types=1);

namespace Rimba\Work\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Rimba\Work\Services\CatalogRegistry;
use Rimba\Work\Services\WorkSeedImportService;

#[Description('Import registered Rimba/Kerja lifecycle catalogs')]
#[Signature('kerja:catalog-import {catalog?}')]
final class ImportWorkCatalogs extends Command
{
    public function __construct(
        private readonly CatalogRegistry $catalogRegistry,
        private readonly WorkSeedImportService $workSeedImportService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $requested = $this->argument('catalog');
        $catalogs = $requested
            ? [(string) $requested => $this->catalogRegistry->path((string) $requested)]
            : $this->catalogRegistry->all();

        if ($catalogs === []) {
            $this->warn('No Kerja catalogs are registered.');

            return self::SUCCESS;
        }

        foreach ($catalogs as $name => $path) {
            $this->info("Importing [{$name}] from [{$path}]...");
            foreach ($this->workSeedImportService->import($path) as $section => $count) {
                $this->line(sprintf('  %-24s %d', $section, $count));
            }
        }

        return self::SUCCESS;
    }
}
