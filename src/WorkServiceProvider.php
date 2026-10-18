<?php

declare(strict_types=1);

namespace Rimba\Work;

use Illuminate\Support\Facades\File;
use Rimba\Base\Services\BitesServiceProvider;
use Rimba\Work\Services\CatalogRegistry;
use Rimba\Work\Services\ConverterRegistry;
use Rimba\Work\Services\EndpointClient;
use Rimba\Work\Services\FlowDefinitionValidator;
use Rimba\Work\Services\HookBindingRunner;
use Rimba\Work\Services\HookPath;
use Rimba\Work\Services\HookRunner;
use Rimba\Work\Services\Import\CatalogImporter;
use Rimba\Work\Services\Import\FlowImporter;
use Rimba\Work\Services\Import\HookImporter;
use Rimba\Work\Services\Import\OrganizationImporter;
use Rimba\Work\Services\Import\PackageImporter;
use Rimba\Work\Services\Import\ReferenceResolver;
use Rimba\Work\Services\TaskEligibilityService;
use Rimba\Work\Services\TaskRendererRegistry;

class WorkServiceProvider extends BitesServiceProvider
{
    protected string $configFile = __DIR__.'/../config/bites.php';

    protected string $viewsPath = __DIR__.'/../resources/views';

    protected function bootPackage(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([__DIR__.'/../setup' => storage_path('setup')], 'work-setup');
        $this->ensureSetupFilesExist();
    }

    protected function registerPackage(): void
    {
        $this->app->singleton(CatalogRegistry::class);
        $this->app->singleton(TaskRendererRegistry::class);
        $this->app->singleton(ConverterRegistry::class);
        $this->app->singleton(HookPath::class);
        $this->app->singleton(EndpointClient::class);
        $this->app->singleton(HookRunner::class);
        $this->app->singleton(HookBindingRunner::class);
        $this->app->singleton(TaskEligibilityService::class);
        $this->app->singleton(FlowDefinitionValidator::class);
        $this->app->singleton(ReferenceResolver::class);
        $this->app->singleton(OrganizationImporter::class);
        $this->app->singleton(CatalogImporter::class);
        $this->app->singleton(PackageImporter::class);
        $this->app->singleton(FlowImporter::class);
        $this->app->singleton(HookImporter::class);
    }

    protected function ensureSetupFilesExist(): void
    {
        $source = __DIR__.'/../setup';
        $destination = storage_path('setup');
        if (! File::isDirectory($source)) {
            return;
        }

        File::ensureDirectoryExists($destination);
        foreach (File::allFiles($source) as $file) {
            $target = $destination.'/'.$file->getRelativePathname();
            if (! File::exists($target)) {
                File::ensureDirectoryExists(dirname($target));
                File::copy($file->getRealPath(), $target);
            }
        }
    }
}
