<?php

declare(strict_types=1);

namespace Rimba\Work;

use Rimba\Base\Services\BitesServiceProvider;

class WorkServiceProvider extends BitesServiceProvider
{
    /*
    |--------------------------------------------------------------------------
    | Bites Package Configuration
    |--------------------------------------------------------------------------
    */

    // protected string $configFile = __DIR__.'/../config/work.php';

    protected string $viewsPath = __DIR__.'/../resources/views';

    protected string $iconsPath = __DIR__.'/../resources/svg';

    /*
     * Keep these shared if your intention is that all RIMBA packages
     * contribute into the same Bites namespace/configuration.
     */
    protected string $configName = 'work';

    protected string $viewNamespace = 'bites';

    protected string $iconSet = 'bites';

    protected string $iconPrefix = 'bites';

    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    protected function registerPackage(): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
