<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Organization\Models\OrgUnit;
use Rimba\Work\Contracts\OrgUnitOwned;
use Rimba\Work\Services\WorkAccessService;

final readonly class WorkPolicy
{
    public function __construct(
        private WorkAccessService $workAccessService,
    ) {}

    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $this->hasManageableUnit(
            $user,
        );
    }

    public function view(
        Authenticatable $user,
        OrgUnitOwned $model,
    ): bool {
        return $this->canManage(
            $user,
            $model,
        );
    }

    public function create(
        Authenticatable $user,
    ): bool {
        return $this->hasManageableUnit(
            $user,
        );
    }

    public function update(
        Authenticatable $user,
        OrgUnitOwned $model,
    ): bool {
        return $this->canManage(
            $user,
            $model,
        );
    }

    public function delete(
        Authenticatable $user,
        OrgUnitOwned $model,
    ): bool {
        return $this->canManage(
            $user,
            $model,
        );
    }

    public function restore(
        Authenticatable $user,
        OrgUnitOwned $model,
    ): bool {
        return $this->canManage(
            $user,
            $model,
        );
    }

    public function forceDelete(
        Authenticatable $user,
        OrgUnitOwned $model,
    ): bool {
        return $this->canManage(
            $user,
            $model,
        );
    }

    private function hasManageableUnit(
        Authenticatable $user,
    ): bool {
        return $this->workAccessService
            ->manageableUnitIds($user) !== [];
    }

    private function canManage(
        Authenticatable $user,
        OrgUnitOwned $model,
    ): bool {
        $unit = $model->owningOrgUnit();

        if (! $unit instanceof OrgUnit) {
            return false;
        }

        return $this->workAccessService->canManageUnit(
            user: $user,
            unit: $unit,
        );
    }
}
