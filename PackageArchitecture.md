Modelling
```text
src/
├── Models/
│   ├── Form.php
│   ├── Lifecycle.php
│   ├── LifecyclePhase.php
│   ├── Offering.php
│   ├── OfferingExecution.php
│   └── Workflow.php
│
├── Concerns/
│   ├── HasLifecycle.php
│   ├── HasOfferings.php
│   ├── HasWorkflow.php
│   └── InteractsWithWork.php
│
├── Policies/
│   ├── FormPolicy.php
│   ├── LifecyclePolicy.php
│   ├── OfferingExecutionPolicy.php
│   ├── OfferingPolicy.php
│   └── WorkflowPolicy.php
│
└── Observers/
    ├── OfferingExecutionObserver.php
    ├── OfferingObserver.php
    └── PackstubWorkflowRunObserver.php
```

Integration-core
```text
src/
├── Actions/
│   ├── BuildOfferingContext.php
│   ├── CancelOfferingExecution.php
│   ├── CompleteOfferingExecution.php
│   ├── CreateOfferingSubject.php
│   ├── ExecuteOffering.php
│   ├── MapOfferingInput.php
│   ├── PrepareOffering.php
│   ├── RecordOfferingExecution.php
│   ├── ResolveOfferingForm.php
│   ├── ResolveOfferingSubject.php
│   ├── ResolveOfferingWorkflow.php
│   ├── StartOfferingWorkflow.php
│   └── ValidateOfferingInput.php
│
├── Contracts/
│   ├── FormProvider.php
│   ├── FormRenderer.php
│   ├── OfferingAuthorizer.php
│   ├── OfferingContextBuilder.php
│   ├── OfferingInputMapper.php
│   ├── SubjectRegistry.php
│   ├── WorkflowProvider.php
│   └── WorkflowRunner.php
│
├── Data/
│   ├── ExecutionResult.php
│   ├── FormDefinition.php
│   ├── MappedOfferingData.php
│   ├── OfferingContext.php
│   ├── OfferingDefinition.php
│   ├── PreparedOffering.php
│   └── WorkflowDefinition.php
│
├── Events/
│   ├── OfferingExecuted.php
│   ├── OfferingExecutionFailed.php
│   ├── OfferingExecutionStarted.php
│   ├── OfferingSubjectCreated.php
│   ├── WorkCompleted.php
│   ├── WorkflowCancelled.php
│   ├── WorkflowCompleted.php
│   └── WorkflowStarted.php
│
├── Exceptions/
│   ├── FormProviderException.php
│   ├── InvalidFormMappingException.php
│   ├── InvalidSubjectException.php
│   ├── OfferingExecutionException.php
│   ├── OfferingUnavailableException.php
│   ├── SubjectResolutionException.php
│   ├── WorkflowProviderException.php
│   └── WorkflowStartException.php
│
├── Facades/
│   └── Work.php
│
├── Observers/
│   └── PackstubWorkflowRunObserver.php
│
├── Providers/
│   ├── Forms/
│   │   ├── LocalFormProvider.php
│   │   └── PackstubFormProvider.php
│   └── Workflows/
│       ├── NullWorkflowProvider.php
│       └── PackstubWorkflowProvider.php
│
├── Registries/
│   ├── FormProviderRegistry.php
│   ├── SubjectRegistry.php
│   └── WorkflowProviderRegistry.php
│
├── Services/
│   ├── FormManager.php
│   ├── OfferingManager.php
│   ├── OfferingMappingService.php
│   ├── OfferingPolicyAuthorizer.php
│   ├── WorkManager.php
│   ├── WorkflowManager.php
│   └── WorkflowRunSynchronizer.php
│
└── WorkServiceProvider.php
```

Seed Architecture
```text
rimba/work/
├── database/
│   └── seeders/
│       ├── WorkDatabaseSeeder.php
│       ├── LifecycleSeeder.php
│       ├── LifecyclePhaseSeeder.php
│       ├── FormSeeder.php
│       ├── WorkflowSeeder.php
│       └── OfferingSeeder.php
│
├── resources/
│   └── seeds/
│       ├── lifecycles.json
│       ├── lifecycle-phases.json
│       ├── forms.json
│       ├── workflows.json
│       └── offerings.json
│
└── src/
    ├── Services/
    │   └── Seed/
    │       ├── JsonSeedLoader.php
    │       ├── LifecycleSynchronizer.php
    │       ├── LifecyclePhaseSynchronizer.php
    │       ├── FormSynchronizer.php
    │       ├── WorkflowSynchronizer.php
    │       └── OfferingSynchronizer.php
    │
    └── Support/
        └── SeedPath.php
```