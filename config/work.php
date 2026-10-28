<?php

declare(strict_types=1);

use Packstub\Flow\Models\WorkflowRun;
use Rimba\Organization\Models\OrgTeam;
use Rimba\Organization\Models\OrgUnit;
use Rimba\People\Models\Staff;
use Rimba\Work\Models\Form;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Models\LifecyclePhase;
use Rimba\Work\Models\Offering;
use Rimba\Work\Models\OfferingExecution;
use Rimba\Work\Models\Workflow;
use Rimba\Work\Providers\Forms\LocalFormProvider;
use Rimba\Work\Providers\Forms\PackstubFormProvider;
use Rimba\Work\Providers\Workflows\NullWorkflowProvider;
use Rimba\Work\Providers\Workflows\PackstubWorkflowProvider;

return [

    'models' => [
        'form' => Form::class,
        'lifecycle' => Lifecycle::class,
        'lifecycle_phase' => LifecyclePhase::class,
        'offering' => Offering::class,
        'offering_execution' => OfferingExecution::class,
        'workflow' => Workflow::class,

        'staff' => Staff::class,
        'org_unit' => OrgUnit::class,
        'org_team' => OrgTeam::class,

        'user' => config(
            'auth.providers.users.model',
        ),
    ],

    'providers' => [
        'forms' => [
            'default' => 'packstub',

            'drivers' => [
                'packstub' => [
                    'class' => PackstubFormProvider::class,

                    /*
                     * Set these to the actual models exposed by your installed
                     * version of packstub/filament-form-builder.
                     */
                    'form_model' => null,
                    'submission_model' => null,

                    'lookup_column' => 'slug',
                    'definition_column' => 'definition',

                    /*
                     * Optional programmatic validator adapter.
                     *
                     * Keep null when Packstub validates and stores the
                     * submission before Work::execute() is called.
                     */
                    'validator' => null,
                ],

                'local' => [
                    'class' => LocalFormProvider::class,
                ],
            ],
        ],

        'workflows' => [
            'default' => 'packstub',

            'drivers' => [
                'packstub' => [
                    'class' => PackstubWorkflowProvider::class,

                    'workflow_model' => Packstub\Flow\Models\Workflow::class,
                    'run_model' => WorkflowRun::class,

                    'lookup_column' => 'id',

                    /*
                     * null delegates to Packstub.
                     * false forces synchronous execution.
                     * true forces queued execution.
                     */
                    'queue' => null,
                ],

                'null' => [
                    'class' => NullWorkflowProvider::class,
                ],
            ],
        ],
    ],

    'subjects' => [
        //
    ],

    'execution' => [
        'transactional' => true,

        'store_input_snapshot' => true,
        'store_context_snapshot' => true,
        'store_mapped_snapshot' => true,

        'default_source' => 'filament',
    ],

    'filament' => [
        'navigation_group' => 'Work',
        'navigation_sort' => 50,

        'show_lifecycles' => true,
        'show_forms' => true,
        'show_workflows' => true,
        'show_offerings' => true,
        'show_executions' => true,
    ],
];
