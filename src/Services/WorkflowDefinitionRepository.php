<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use RuntimeException;

class WorkflowDefinitionRepository
{
    public function all(): Collection
    {
        $directory = $this->directory();

        if (! File::isDirectory($directory)) {
            return collect();
        }

        return collect(File::glob($directory.'/*.json'))
            ->map(fn (string $path): array => $this->loadFile($path))
            ->sortBy('title')
            ->values();
    }

    public function find(string $slug): array
    {
        $path = $this->pathFor($slug);

        if (! File::exists($path)) {
            throw new RuntimeException(
                "SIPOC Workflow [{$slug}] was not found."
            );
        }

        return $this->loadFile($path);
    }

    public function save(array $definition): array
    {
        app(WorkflowDefinitionValidator::class)->assert($definition);

        $slug = $this->normalizeSlug(
            (string) ($definition['slug'] ?? '')
        );

        $definition['slug'] = $slug;

        File::ensureDirectoryExists($this->directory());

        File::put(
            $this->pathFor($slug),
            json_encode(
                $definition,
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_THROW_ON_ERROR
            ).PHP_EOL,
            true
        );

        return $this->find($slug);
    }

    public function delete(string $slug): void
    {
        $path = $this->pathFor($slug);

        if (! File::exists($path)) {
            throw new RuntimeException(
                "SIPOC Workflow [{$slug}] was not found."
            );
        }

        File::delete($path);
    }

    private function loadFile(string $path): array
    {
        $definition = json_decode(
            File::get($path),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (! is_array($definition)) {
            throw new RuntimeException(
                basename($path).' must contain a JSON object.'
            );
        }

        app(WorkflowDefinitionValidator::class)->assert($definition);

        $filename = pathinfo($path, PATHINFO_FILENAME);

        if ($filename !== $definition['slug']) {
            throw new RuntimeException(
                "Filename [{$filename}] does not match slug "
                ."[{$definition['slug']}]."
            );
        }

        return $definition;
    }

    private function directory(): string
    {
        return rtrim(
            (string) config('bites.sipoc.setup_path'),
            DIRECTORY_SEPARATOR
        ).DIRECTORY_SEPARATOR.'workflows';
    }

    private function pathFor(string $slug): string
    {
        return $this->directory()
            .DIRECTORY_SEPARATOR
            .$this->normalizeSlug($slug)
            .'.json';
    }

    private function normalizeSlug(string $slug): string
    {
        $slug = trim($slug);

        if (
            $slug === ''
            || preg_match(
                '/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/',
                $slug
            ) !== 1
        ) {
            throw new InvalidArgumentException(
                "Invalid Workflow slug [{$slug}]."
            );
        }

        return $slug;
    }
}
