<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\Offering;

class OfferingPolicy
{
    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $user->can(
            'work.offering.view-any',
        );
    }

    public function view(
        Authenticatable $user,
        Offering $offering,
    ): bool {
        return $user->can(
            'work.offering.view',
        );
    }

    public function create(
        Authenticatable $user,
    ): bool {
        return $user->can(
            'work.offering.create',
        );
    }

    public function update(
        Authenticatable $user,
        Offering $offering,
    ): bool {
        return $user->can(
            'work.offering.update',
        );
    }

    public function delete(
        Authenticatable $user,
        Offering $offering,
    ): bool {
        return $user->can(
            'work.offering.delete',
        );
    }

    public function execute(
        Authenticatable $user,
        Offering $offering,
    ): bool {
        if (! $offering->is_active) {
            return false;
        }

        return (bool) $user->can('work.offering.execute');
    }
}
