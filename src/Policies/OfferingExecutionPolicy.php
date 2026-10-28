<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\OfferingExecution;

class OfferingExecutionPolicy
{
    public $access;

    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $user->can(
            'work.execution.view-any',
        );
    }

    public function view(
        Authenticatable $user,
        OfferingExecution $execution,
    ): bool {
        if (
            (string) $user->getAuthIdentifier()
            === (string) $execution->user_id
        ) {
            return true;
        }

        return $execution->orgUnit !== null
            && $this->access->canManageUnit(
                $user,
                $execution->orgUnit,
            );
    }

    public function cancel(
        Authenticatable $user,
        OfferingExecution $execution,
    ): bool {
        if ($execution->isTerminal()) {
            return false;
        }

        return $user->can(
            'work.execution.cancel',
        );
    }

    public function retry(
        Authenticatable $user,
        OfferingExecution $execution,
    ): bool {
        if (! $execution->isFailed()) {
            return false;
        }

        return $user->can(
            'work.execution.retry',
        );
    }
}
