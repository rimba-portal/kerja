<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\People\Models\Staff;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Services\WorkAccessService;

final readonly class LifecyclePolicy
{
    public function __construct(
        private WorkAccessService $workAccessService,
    ) {}

    /**
     * Any authenticated User with a Staff record
     * may see the lifecycle catalog.
     */
    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $this->workAccessService->staff(
            $user,
        ) instanceof Staff;
    }

    /**
     * Lifecycle is a global reference catalog,
     * not owned by OrgUnit.
     */
    public function view(
        Authenticatable $user,
    ): bool {
        return $this->viewAny(
            $user,
        );
    }

    /**
     * Team Panel users do not create global
     * lifecycle definitions.
     */
    public function create(): bool
    {
        return false;
    }

    public function update(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }

    public function restore(): bool
    {
        return false;
    }

    public function forceDelete(): bool
    {
        return false;
    }
}
