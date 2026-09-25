<?php

declare(strict_types=1);

namespace Rimba\Sipoc;

use Illuminate\Support\Facades\File;
use Rimba\Base\Services\BitesServiceProvider;
use Rimba\Work\Services\ActorResolverService;
use Rimba\Work\Services\HandlerRegistry;
use Rimba\Work\Services\WorkflowDefinitionRepository;
use Rimba\Work\Services\WorkflowDefinitionValidator;

class SipocServiceProvider extends BitesServiceProvider
{
    protected function registerPackage(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sipoc.php',
            'sipoc'
        );

        /*
         * Compact bootstrap files contain multiple classes.
         * Split them into PSR-4 files before production release.
         */
        foreach ([
            __DIR__.'/Enums/SipocEnums.php',
            __DIR__.'/Models/SipocModels.php',
            __DIR__.'/Services/SipocServices.php',
            __DIR__.'/Actions/SipocActions.php',
        ] as $segment) {
            require_once $segment;
        }

        $this->app->singleton(WorkflowDefinitionValidator::class);
        $this->app->singleton(WorkflowDefinitionRepository::class);
        $this->app->singleton(ActorResolverService::class);
        $this->app->singleton(HandlerRegistry::class);

        $this->app->alias(
            WorkflowDefinitionRepository::class,
            'sipoc.definitions'
        );
    }

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(
            __DIR__.'/../database/migrations'
        );

        $this->publishes([
            __DIR__.'/../config/sipoc.php' => config_path('sipoc.php'),
        ], 'sipoc-config');

        $this->publishes([
            __DIR__.'/../setup/sipoc' => storage_path('setup/sipoc'),
        ], 'sipoc-setup');

        if (! File::isDirectory(config('bites.sipoc.setup_path'))) {
            File::ensureDirectoryExists(
                config('bites.sipoc.setup_path').'/workflows'
            );

            File::ensureDirectoryExists(
                config('bites.sipoc.setup_path').'/templates'
            );
        }
    }
}
