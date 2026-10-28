<?php

declare(strict_types=1);

namespace Rimba\Work\Support;

class SeedPath
{
    public static function make(
        string $file,
    ): string {
        return dirname(__DIR__, 2)
            .'/resources/seeds/'
            .ltrim($file, '/');
    }
}
