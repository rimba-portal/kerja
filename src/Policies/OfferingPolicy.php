<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\Offering;
use Rimba\Work\Services\WorkAccessService;

final readonly class OfferingPolicy
{
    public function __construct(
        private WorkAccessService $workAccessService,
    ) {}

    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $this->workAccessService->staff($user) !== null;
    }

    public function view(
        Authenticatable $user,
        Offering $offering,
    ): bool {
        return $this->workAccessService->staff($user) !== null
            && $offering->is_active;
    }

    public function create(
        Authenticatable $user,
    ): bool {
        return $this->workAccessService
            ->manageableUnitIds($user) !== [];
    }

    public function update(
        Authenticatable $user,
        Offering $offering,
    ): bool {
        return $offering->orgUnit !== null
            && $this->workAccessService->canManageUnit(
                $user,
                $offering->orgUnit,
            );
    }

    public function delete(
        Authenticatable $user,
        Offering $offering,
    ): bool {
        return $this->update(
            $user,
            $offering,
        );
    }

    public function execute(
        Authenticatable $user,
        Offering $offering,
    ): bool {
        return $this->workAccessService->staff($user) !== null
            && $offering->is_active
            && $offering->availability->value
                === 'available';
    }
}
