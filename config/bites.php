<?php

declare(strict_types=1);

use Rimba\Sipoc\Models\Artifact;
use Rimba\Sipoc\Models\Task;
use Rimba\Sipoc\Models\Transition;
use Rimba\Sipoc\Models\WorkflowInstance;

return [
    'sipoc' => [

        /*
     * Installed definitions editable by the application.
     */
        'setup_path' => storage_path('setup/sipoc'),

        /*
     * Definitions distributed with this package.
     */
        'package_setup_path' => dirname(__DIR__).'/setup/sipoc',

        'system_actor' => 'Rimba',

        'models' => [
            'workflow_instance' => WorkflowInstance::class,
            'task' => Task::class,
            'artifact' => Artifact::class,
            'transition' => Transition::class,
        ],

        /*
     * Handler aliases referenced by JSON WorkPackages.
     *
     * Consuming packages may merge additional handlers.
     */
        'handlers' => [
            // 'leave.capture_context' => CaptureEmployeeContext::class,
        ],

        'permissions' => [
            'enforce' => false,
            'abilities' => [
                'init',
                'view',
                'work',
                'own',
                'administer',
            ],
        ],
    ],
];
