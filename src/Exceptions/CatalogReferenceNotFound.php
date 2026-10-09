<?php

declare(strict_types=1);

namespace Rimba\Work\Exceptions;

use RuntimeException;

final class CatalogReferenceNotFound extends RuntimeException
{
    public static function for(string $type, string $code): self
    {
        return new self("{$type} [{$code}] was not found.");
    }
}
