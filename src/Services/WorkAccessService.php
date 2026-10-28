<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Rimba\Can\Support\RbacRoleName;
use Rimba\Organization\Models\OrgTeam;
use Rimba\Organization\Models\OrgUnit;
use Rimba\People\Models\Staff;

final class WorkAccessService
{
    public function staff(
        ?Authenticatable $user,
    ): ?Staff {
        if (! $user instanceof Authenticatable) {
            return null;
        }

        $staff = $user->staff;

        return $staff instanceof Staff
            ? $staff
            : null;
    }

    public function isTacticianOfTeam(
        ?Authenticatable $user,
        OrgTeam $team,
    ): bool {
        $staff = $this->staff($user);

        if (! $staff instanceof Staff) {
            return false;
        }

        return $staff->hasRole(
            RbacRoleName::team(
                scope: $team->nick(),
                role: 'tactician',
            ),
        );
    }

    public function canManageUnit(
        ?Authenticatable $user,
        OrgUnit $unit,
    ): bool {
        $staff = $this->staff($user);

        if (! $staff instanceof Staff) {
            return false;
        }

        return $unit
            ->orgTeams()
            ->get()
            ->contains(
                fn (OrgTeam $team): bool => $this->isTacticianOfTeam(
                    user: $user,
                    team: $team,
                ),
            );
    }

    public function manageableUnits(
        ?Authenticatable $user,
    ): Collection {
        $ids = $this->manageableUnitIds(
            $user,
        );

        if ($ids === []) {
            return collect();
        }

        return OrgUnit::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get();
    }

    public function manageableUnitIds(
        ?Authenticatable $user,
    ): array {
        $staff = $this->staff($user);

        if (! $staff instanceof Staff) {
            return [];
        }

        return OrgTeam::query()
            ->select([
                'id',
                'org_unit_id',
                'name',
                'code',
            ])
            ->get()
            ->filter(
                fn (OrgTeam $team): bool => $staff->hasRole(
                    RbacRoleName::team(
                        scope: $team->nick(),
                        role: 'tactician',
                    ),
                ),
            )
            ->pluck('org_unit_id')
            ->unique()
            ->values()
            ->all();
    }
}
