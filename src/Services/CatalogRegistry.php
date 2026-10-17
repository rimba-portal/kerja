<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use InvalidArgumentException;

final class CatalogRegistry
{
    private array $catalogs = [];

    public function register(string $name, string $path): void
    {
        if ($name === '') {
            throw new InvalidArgumentException('Catalog name cannot be empty.');
        }

        $this->catalogs[$name] = $path;
    }

    public function path(string $name): string
    {
        return $this->catalogs[$name]
            ?? throw new InvalidArgumentException("Kerja catalog [{$name}] is not registered.");
    }

    public function all(): array
    {
        return $this->catalogs;
    }
}
