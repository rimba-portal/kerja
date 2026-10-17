<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use InvalidArgumentException;

final class TaskRendererRegistry
{
    private array $renderers = [];

    public function register(string $packageCode, string $rendererClass): void
    {
        if (! class_exists($rendererClass)) {
            throw new InvalidArgumentException("Task renderer class [{$rendererClass}] does not exist.");
        }

        $this->renderers[$packageCode] = $rendererClass;
    }

    public function has(string $packageCode): bool
    {
        return isset($this->renderers[$packageCode]);
    }

    public function resolve(string $packageCode): string
    {
        return $this->renderers[$packageCode]
            ?? throw new InvalidArgumentException("No Task renderer registered for Package [{$packageCode}].");
    }

    public function all(): array
    {
        return $this->renderers;
    }
}
