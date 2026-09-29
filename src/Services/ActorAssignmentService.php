<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\WorkPackage;

final class ActorAssignmentService
{
    public function resolve(WorkPackage $w): ?Model
    {
        $m = config('bites.kerja.models.staff');
        $r = config('bites.kerja.organization.staff_job_roles_relation', 'jobRoles');

        return $m::query()->whereHas($r, fn ($q) => $q->whereKey($w->actor_job_role_id))->first();
    }
}
