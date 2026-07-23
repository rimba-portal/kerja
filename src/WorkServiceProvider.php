<?php

declare(strict_types=1);

namespace Rimba\Work;

use Rimba\Base\Services\BitesServiceProvider;

class WorkServiceProvider extends BitesServiceProvider
{
    protected string $configFile = __DIR__.'/../config/bites.php';

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        //
    }

    protected function registerPackage(): void
    {
        //
    }
}
