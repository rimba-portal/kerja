<?php

declare(strict_types=1);

namespace Rimba\Work\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Rimba\Work\Services\WorkSeedImportService;

#[Description('Import WorkFlow and WorkPackage definitions from JSON')]
#[Signature('rimba:seed {source}')]
class SeedWorkCatalog extends Command
{
    public function __construct(
        private readonly WorkSeedImportService $workSeedImportService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $source = (string) $this->argument('source');

        $this->info(
            sprintf(
                'Importing work catalog from [%s]...',
                $source
            )
        );

        $result = $this->workSeedImportService->import($source);

        $this->info(
            sprintf(
                'Imported %d activity types, %d business objects, %d work packages, %d work flows.',
                $result['activity_types'],
                $result['business_objects'],
                $result['work_packages'],
                $result['work_flows'],
            )
        );

        return self::SUCCESS;
    }
}
