<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use App\Models\User;

final class WorkPackagePolicy
{
    public function viewAny(
        User $user,
    ): bool {
        return $user->can('work-package.viewAny');
    }

    public function view(
        User $user,
    ): bool {
        return $user->can('work-package.view');
    }

    public function create(
        User $user,
    ): bool {
        return $user->can('work-package.create');
    }

    public function update(
        User $user,
    ): bool {
        return $user->can('work-package.update');
    }

    public function delete(
        User $user,
    ): bool {
        return $user->can('work-package.delete');
    }

    public function start(
        User $user,
    ): bool {
        return $user->can('work-package.start');
    }
}
