<?php

declare(strict_types=1);

namespace Rimba\Work\Support;

final class SeedPath
{
    public static function directory(): string
    {
        return dirname(__DIR__, 2)
            .'/resources/seeds';
    }
}
