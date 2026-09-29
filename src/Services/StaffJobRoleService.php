<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;

final class StaffJobRoleService
{
    public function ids(Model $staff)
    {
        $r = config('bites.kerja.organization.staff_job_roles_relation', 'jobRoles');

        return $staff->{$r}()->pluck('id');
    }
}
