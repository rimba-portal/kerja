<?php

declare(strict_types=1);

namespace Rimba\Work;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Actions\BuildOfferingContext;
use Rimba\Work\Contracts\OfferingAuthorizer;
use Rimba\Work\Contracts\OfferingContextBuilder;
use Rimba\Work\Contracts\OfferingInputMapper;
use Rimba\Work\Contracts\SubjectRegistry as SubjectRegistryContract;
use Rimba\Work\Contracts\WorkflowRunner;
use Rimba\Work\Observers\PackstubWorkflowRunObserver;
use Rimba\Work\Providers\Forms\LocalFormProvider;
use Rimba\Work\Providers\Forms\PackstubFormProvider;
use Rimba\Work\Providers\Workflows\NullWorkflowProvider;
use Rimba\Work\Providers\Workflows\PackstubWorkflowProvider;
use Rimba\Work\Registries\FormProviderRegistry;
use Rimba\Work\Registries\SubjectRegistry;
use Rimba\Work\Registries\WorkflowProviderRegistry;
use Rimba\Work\Services\OfferingMappingService;
use Rimba\Work\Services\OfferingPolicyAuthorizer;
use Rimba\Work\Services\WorkManager;
use Rimba\Work\Services\WorkflowManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Illuminate\Support\Facades\Gate;
use Rimba\Work\Models\Form;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Models\Offering;
use Rimba\Work\Models\OfferingExecution;
use Rimba\Work\Models\Workflow;
use Rimba\Work\Observers\OfferingExecutionObserver;
use Rimba\Work\Observers\OfferingObserver;
use Rimba\Work\Policies\FormPolicy;
use Rimba\Work\Policies\LifecyclePolicy;
use Rimba\Work\Policies\OfferingExecutionPolicy;
use Rimba\Work\Policies\OfferingPolicy;
use Rimba\Work\Policies\WorkflowPolicy;

class WorkServiceProvider extends PackageServiceProvider
{
    public function configurePackage(
        Package $package,
    ): void {
        $package
            ->name('work')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('web')
            ->hasMigrations([
                'create_work_lifecycles_table',
                'create_work_lifecycle_phases_table',
                'create_work_forms_table',
                'create_work_workflows_table',
                'create_work_offerings_table',
                'create_work_offering_executions_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(
            SubjectRegistryContract::class,
            SubjectRegistry::class,
        );

        $this->app->alias(
            SubjectRegistryContract::class,
            SubjectRegistry::class,
        );

        $this->app->singleton(
            FormProviderRegistry::class,
        );

        $this->app->singleton(
            WorkflowProviderRegistry::class,
        );

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

        $this->app->singleton(
            WorkManager::class,
        );

        $this->app->alias(
            WorkManager::class,
            'rimba.work',
        );
    }

    public function packageBooted(): void
    {
        $this->registerFormProviders();
        $this->registerWorkflowProviders();
        $this->registerConfiguredSubjects();

        $this->registerPolicies();
        $this->registerObservers();

        $this->registerPackstubRunObserver();
    }
    protected function registerObservers(): void
    {
        Offering::observe(
            OfferingObserver::class,
        );

        OfferingExecution::observe(
            OfferingExecutionObserver::class,
        );
    }
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
            Offering::class,
            OfferingPolicy::class,
        );

        Gate::policy(
            OfferingExecution::class,
            OfferingExecutionPolicy::class,
        );

        Gate::policy(
            Workflow::class,
            WorkflowPolicy::class,
        );
    }
    protected function registerFormProviders(): void
    {
        $registry = $this->app->make(
            FormProviderRegistry::class,
        );

        $registry
            ->register(
                $this->app->make(
                    PackstubFormProvider::class,
                ),
            )
            ->register(
                $this->app->make(
                    LocalFormProvider::class,
                ),
            );
    }

    protected function registerWorkflowProviders(): void
    {
        $registry = $this->app->make(
            WorkflowProviderRegistry::class,
        );

        $registry
            ->register(
                $this->app->make(
                    PackstubWorkflowProvider::class,
                ),
            )
            ->register(
                $this->app->make(
                    NullWorkflowProvider::class,
                ),
            );
    }

    protected function registerConfiguredSubjects(): void
    {
        $registry = $this->app->make(
            SubjectRegistryContract::class,
        );

        foreach (
            config('work.subjects', [])
            as $alias => $modelClass
        ) {
            $registry->register(
                alias: $alias,
                modelClass: $modelClass,
            );
        }
    }

    protected function registerPackstubRunObserver(): void
    {
        $modelClass = config(
            'work.providers.workflows.drivers.packstub.run_model',
            \Packstub\Flow\Models\WorkflowRun::class,
        );

        if (
            is_string($modelClass)
            && is_a($modelClass, Model::class, true)
        ) {
            $modelClass::observe(
                PackstubWorkflowRunObserver::class,
            );
        }
    }
}
