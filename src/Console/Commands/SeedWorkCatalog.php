<?php

declare(strict_types=1);

namespace Rimba\Work\Console\Commands;

use Illuminate\Console\Command;
use Rimba\Work\Services\WorkSeedImportService;

class SeedWorkCatalog extends Command
{
    protected $signature = 'rimba:seed {source}';

    protected $description =
        'Import WorkFlow and WorkPackage definitions from JSON';

    public function __construct(
        private readonly WorkSeedImportService $importService,
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

        $result = $this->importService->import($source);

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
