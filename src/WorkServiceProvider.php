<?php

declare(strict_types=1);

namespace Rimba\Work;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Rimba\Base\Services\BitesServiceProvider;
use Rimba\Work\Actions\BuildOfferingContext;
use Rimba\Work\Contracts\OfferingAuthorizer;
use Rimba\Work\Contracts\OfferingContextBuilder;
use Rimba\Work\Contracts\OfferingInputMapper;
use Rimba\Work\Contracts\SubjectRegistry as SubjectRegistryContract;
use Rimba\Work\Contracts\WorkflowRunner;
use Rimba\Work\Livewire\DynamicOfferingForm;
use Rimba\Work\Models\Form;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Models\Offering;
use Rimba\Work\Models\OfferingExecution;
use Rimba\Work\Models\Workflow;
use Rimba\Work\Observers\OfferingExecutionObserver;
use Rimba\Work\Observers\PackstubWorkflowRunObserver;
use Rimba\Work\Policies\FormPolicy;
use Rimba\Work\Policies\LifecyclePolicy;
use Rimba\Work\Policies\OfferingExecutionPolicy;
use Rimba\Work\Policies\OfferingPolicy;
use Rimba\Work\Policies\WorkflowPolicy;
use Rimba\Work\Providers\Forms\LocalFormProvider;
use Rimba\Work\Providers\Forms\PackstubFormProvider;
use Rimba\Work\Providers\Workflows\NullWorkflowProvider;
use Rimba\Work\Providers\Workflows\PackstubWorkflowProvider;
use Rimba\Work\Registries\FormProviderRegistry;
use Rimba\Work\Registries\SubjectRegistry;
use Rimba\Work\Registries\WorkflowProviderRegistry;
use Rimba\Work\Services\OfferingMappingService;
use Rimba\Work\Services\OfferingPolicyAuthorizer;
use Rimba\Work\Services\WorkAccessService;
use Rimba\Work\Services\WorkflowManager;
use Rimba\Work\Services\WorkManager;

class WorkServiceProvider extends BitesServiceProvider
{
    /*
    |--------------------------------------------------------------------------
    | Bites Package Configuration
    |--------------------------------------------------------------------------
    */

    protected string $configFile = __DIR__.'/../config/work.php';

    protected string $viewsPath = __DIR__.'/../resources/views';

    protected string $iconsPath = __DIR__.'/../resources/svg';

    /*
     * Keep these shared if your intention is that all RIMBA packages
     * contribute into the same Bites namespace/configuration.
     */
    protected string $configName = 'work';

    protected string $viewNamespace = 'work';

    /*
     * Work currently does not need its own icon set.
     *
     * If Work has resources/svg, change these to:
     *
     * protected string $iconSet = 'work';
     * protected string $iconPrefix = 'work';
     */
    protected string $iconSet = 'bites';

    protected string $iconPrefix = 'bites';

    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    protected function registerPackage(): void
    {
        $this->registerRegistries();
        $this->registerContracts();
        $this->registerManagers();
    }

    protected function registerRegistries(): void
    {
        /*
         * One concrete SubjectRegistry singleton.
         *
         * The SubjectRegistry interface points to the exact same instance.
         */
        $this->app->singleton(
            SubjectRegistry::class,
        );

        $this->app->alias(
            SubjectRegistry::class,
            SubjectRegistryContract::class,
        );

        $this->app->singleton(
            FormProviderRegistry::class,
        );

        $this->app->singleton(
            WorkflowProviderRegistry::class,
        );
    }

    protected function registerContracts(): void
    {
        $this->app->bind(
            OfferingInputMapper::class,
            OfferingMappingService::class,
        );

        $this->app->bind(
            OfferingContextBuilder::class,
            BuildOfferingContext::class,
        );

        $this->app->bind(
            OfferingAuthorizer::class,
            OfferingPolicyAuthorizer::class,
        );

        $this->app->bind(
            WorkflowRunner::class,
            WorkflowManager::class,
        );
    }

    protected function registerManagers(): void
    {
        $this->app->singleton(
            WorkAccessService::class,
        );

        $this->app->singleton(
            WorkManager::class,
        );

        $this->app->alias(
            WorkManager::class,
            'rimba.work',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    protected function bootPackage(): void
    {
        $this->registerFormProviders();
        $this->registerWorkflowProviders();
        $this->registerConfiguredSubjects();

        $this->registerPolicies();
        $this->registerObservers();

        $this->registerPackstubWorkflowRunObserver();

        $this->registerLivewireComponents();
    }

    /*
    |--------------------------------------------------------------------------
    | Form Providers
    |--------------------------------------------------------------------------
    */

    protected function registerFormProviders(): void
    {
        $registry = $this->app->make(
            FormProviderRegistry::class,
        );

        $registry->register(
            $this->app->make(
                PackstubFormProvider::class,
            ),
        );

        $registry->register(
            $this->app->make(
                LocalFormProvider::class,
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow Providers
    |--------------------------------------------------------------------------
    */

    protected function registerWorkflowProviders(): void
    {
        $registry = $this->app->make(
            WorkflowProviderRegistry::class,
        );

        $registry->register(
            $this->app->make(
                PackstubWorkflowProvider::class,
            ),
        );

        $registry->register(
            $this->app->make(
                NullWorkflowProvider::class,
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Subject Registry
    |--------------------------------------------------------------------------
    */

    protected function registerConfiguredSubjects(): void
    {
        $registry = $this->app->make(
            SubjectRegistryContract::class,
        );

        foreach (
            config('work.subjects', []) as $alias => $modelClass
        ) {
            $registry->register(
                alias: $alias,
                modelClass: $modelClass,
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Policies
    |--------------------------------------------------------------------------
    */

    protected function registerPolicies(): void
    {
        Gate::policy(
            Form::class,
            FormPolicy::class,
        );

        Gate::policy(
            Lifecycle::class,
            LifecyclePolicy::class,
        );

        Gate::policy(
            Workflow::class,
            WorkflowPolicy::class,
        );

        Gate::policy(
            Offering::class,
            OfferingPolicy::class,
        );

        Gate::policy(
            OfferingExecution::class,
            OfferingExecutionPolicy::class,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Observers
    |--------------------------------------------------------------------------
    */

    protected function registerObservers(): void
    {
        OfferingExecution::observe(
            OfferingExecutionObserver::class,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Packstub Workflow Run Observer
    |--------------------------------------------------------------------------
    */

    protected function registerPackstubWorkflowRunObserver(): void
    {
        $modelClass = config(
            'work.providers.workflows.drivers.packstub.run_model',
        );

        if (
            ! is_string($modelClass)
            || ! is_a(
                $modelClass,
                Model::class,
                true,
            )
        ) {
            return;
        }

        $modelClass::observe(
            PackstubWorkflowRunObserver::class,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    */

    protected function registerLivewireComponents(): void
    {
        Livewire::component(
            'work.dynamic-offering-form',
            DynamicOfferingForm::class,
        );
    }
}
