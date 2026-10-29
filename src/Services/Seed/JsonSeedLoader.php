<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use JsonException;
use Rimba\Work\Exceptions\SeedSynchronizationException;

final class JsonSeedLoader
{
    public function loadDocument(string $path): array
    {
        if (! is_file($path)) {
            throw new SeedSynchronizationException(
                "Seed file [{$path}] does not exist."
            );
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new SeedSynchronizationException(
                "Unable to read seed file [{$path}]."
            );
        }

        try {
            $data = json_decode(
                $contents,
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $jsonException) {
            throw new SeedSynchronizationException("Invalid JSON in [{$path}]: {$jsonException->getMessage()}", $jsonException->getCode(), previous: $jsonException);
        }

        if (! is_array($data)) {
            throw new SeedSynchronizationException(
                "Seed file [{$path}] must contain a JSON object."
            );
        }

        return $data;
    }
}
