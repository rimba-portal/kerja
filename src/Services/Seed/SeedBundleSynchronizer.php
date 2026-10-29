<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Illuminate\Support\Facades\DB;
use Rimba\Work\Exceptions\SeedSynchronizationException;

final class SeedBundleSynchronizer
{
    public function __construct(
        private readonly JsonSeedLoader $jsonSeedLoader,
        private readonly FormSynchronizer $formSynchronizer,
        private readonly WorkflowSynchronizer $workflowSynchronizer,
        private readonly OfferingSynchronizer $offeringSynchronizer,
    ) {}

    public function syncFile(string $path): void
    {
        $bundle = $this->jsonSeedLoader->loadDocument($path);

        $this->validate($bundle, $path);

        DB::transaction(function () use ($bundle): void {
            $this->formSynchronizer->sync(
                $bundle['forms'] ?? [],
            );

            $this->workflowSynchronizer->sync(
                $bundle['workflows'] ?? [],
            );

            $this->offeringSynchronizer->sync(
                $bundle['offerings'] ?? [],
            );
        });
    }

    private function validate(
        array $bundle,
        string $path,
    ): void {
        if (($bundle['schema'] ?? null) !== 'rimba.work.seed') {
            throw new SeedSynchronizationException(
                "Invalid work seed schema in [{$path}]."
            );
        }

        if (($bundle['version'] ?? null) !== 1) {
            throw new SeedSynchronizationException(
                "Unsupported work seed version in [{$path}]."
            );
        }

        if (blank($bundle['code'] ?? null)) {
            throw new SeedSynchronizationException(
                "Work seed bundle [{$path}] requires [code]."
            );
        }

        foreach ([
            'forms',
            'workflows',
            'offerings',
        ] as $section) {
            if (
                isset($bundle[$section])
                && ! is_array($bundle[$section])
            ) {
                throw new SeedSynchronizationException(
                    "Bundle section [{$section}] must be an array."
                );
            }
        }
    }
}
