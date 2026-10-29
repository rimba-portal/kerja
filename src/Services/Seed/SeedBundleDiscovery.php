<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

final class SeedBundleDiscovery
{
    public function discover(string $directory): Collection
    {
        if (! is_dir($directory)) {
            return collect();
        }

        $finder = Finder::create()
            ->files()
            ->in($directory)
            ->name('*.json')
            ->sortByName();

        return collect(iterator_to_array($finder))
            ->map(
                fn ($file): string => $file->getRealPath()
            )
            ->filter()
            ->values();
    }
}
