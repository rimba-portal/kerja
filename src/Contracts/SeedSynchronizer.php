<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

interface SeedSynchronizer
{
    public function sync(
        array $records,
    ): void;
}
