<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\OfferingExecution;
use Rimba\Work\Services\WorkAccessService;

final readonly class OfferingExecutionPolicy
{
    public function __construct(
        private WorkAccessService $workAccessService,
    ) {}

    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $this->workAccessService->staff(
            $user,
        ) !== null;
    }

    public function view(
        Authenticatable $user,
        OfferingExecution $execution,
    ): bool {
        if ($this->isRequester($user, $execution)) {
            return true;
        }

        $unit = $execution->orgUnit;

        if ($unit === null) {
            return false;
        }

        return $this->workAccessService->canManageUnit(
            user: $user,
            unit: $unit,
        );
    }

    public function create(
        Authenticatable $user,
    ): bool {
        return $this->workAccessService->staff(
            $user,
        ) !== null;
    }

    public function cancel(
        Authenticatable $user,
        OfferingExecution $execution,
    ): bool {
        if ($execution->isTerminal()) {
            return false;
        }

        return $this->isRequester(
            $user,
            $execution,
        );
    }

    public function retry(
        Authenticatable $user,
        OfferingExecution $execution,
    ): bool {
        if (! $execution->isFailed()) {
            return false;
        }

        $unit = $execution->orgUnit;

        if ($unit === null) {
            return false;
        }

        return $this->workAccessService->canManageUnit(
            user: $user,
            unit: $unit,
        );
    }

    private function isRequester(
        Authenticatable $user,
        OfferingExecution $execution,
    ): bool {
        return (string) $user->getAuthIdentifier()
            === (string) $execution->user_id;
    }
}
