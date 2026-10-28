<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\Lifecycle;

class LifecyclePolicy
{
    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $user->can(
            'work.lifecycle.view-any',
        );
    }

    public function view(
        Authenticatable $user,
        Lifecycle $lifecycle,
    ): bool {
        return $user->can(
            'work.lifecycle.view',
        );
    }

    public function create(
        Authenticatable $user,
    ): bool {
        return $user->can(
            'work.lifecycle.create',
        );
    }

    public function update(
        Authenticatable $user,
        Lifecycle $lifecycle,
    ): bool {
        return $user->can(
            'work.lifecycle.update',
        );
    }

    public function delete(
        Authenticatable $user,
        Lifecycle $lifecycle,
    ): bool {
        return $user->can(
            'work.lifecycle.delete',
        );
    }
}
