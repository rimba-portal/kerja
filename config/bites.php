<?php

declare(strict_types=1);

use Rimba\Organization\Models\OrgTeam;
use Rimba\People\Models\Staff;
use Rimba\Position\Models\JobRole;

return [
    'kerja' => [
        'models' => [
            'org_team' => OrgTeam::class,
            'job_role' => JobRole::class,
            'staff' => Staff::class,
        ],
        'organization' => [
            'staff_job_roles_relation' => 'jobRoles',
            'job_role_team_foreign_key' => 'org_team_id',
        ],
    ],
];
