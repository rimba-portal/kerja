<?php

declare(strict_types=1);

namespace Rimba\Work;

use Illuminate\Support\Facades\File;
use Rimba\Base\Services\BitesServiceProvider;
use Rimba\Work\Services\ActorResolverService;
use Rimba\Work\Services\HandlerRegistry;
use Rimba\Work\Services\WorkflowDefinitionRepository;
use Rimba\Work\Services\WorkflowDefinitionValidator;

class WorkServiceProvider extends BitesServiceProvider
{
    protected string $configFile = __DIR__.'/../config/bites.php';

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([__DIR__.'/../setup' => storage_path('setup')], 'work-setup');
        $this->ensureSetupFilesExist();

    }

    protected function registerPackage(): void
    {
        $this->app->singleton(WorkflowDefinitionValidator::class);
        $this->app->singleton(WorkflowDefinitionRepository::class);
        $this->app->singleton(ActorResolverService::class);
        $this->app->singleton(HandlerRegistry::class);
        $this->app->alias(WorkflowDefinitionRepository::class, 'sipoc.definitions');

    }

    protected function ensureSetupFilesExist(): void
    {
        $source = __DIR__.'/../setup';
        $destination = storage_path('setup');
        File::ensureDirectoryExists($destination);
        foreach (File::allFiles($source) as $file) {
            $relativePath = $file->getRelativePathname();
            $target = $destination.'/'.$relativePath;
            if (! File::exists($target)) {
                File::ensureDirectoryExists(dirname($target));
                File::copy($file->getRealPath(), $target);
            }
        }
    }
}
