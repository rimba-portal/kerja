<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use InvalidArgumentException;
use Rimba\Work\Models\WorkPackage;

final class WorkPackageObserver
{
    public function saving(WorkPackage $w): void
    {
        $m = config('bites.kerja.models.job_role');
        $fk = config('bites.kerja.organization.job_role_team_foreign_key', 'org_team_id');
        if (! $m::query()
            ->whereKey($w->actor_job_role_id)
            ->where($fk, $w->org_team_id)
            ->exists()) {
            throw new InvalidArgumentException('Actor JobRole must belong to owning OrgTeam.');
        }
    }
}
