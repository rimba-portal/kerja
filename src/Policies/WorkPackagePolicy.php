<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use App\Models\User;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\TaskInstance;
use Rimba\Work\Models\WorkPackage;
use Rimba\Work\Models\WorkPackageInstance;

final class WorkPackagePolicy
{
    public function viewAny(
        User $user,
    ): bool {
        return $user->can('work-package.viewAny');
    }

    public function view(
        User $user,
        WorkPackage $workPackage,
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
        WorkPackage $workPackage,
    ): bool {
        return $user->can('work-package.update');
    }

    public function delete(
        User $user,
        WorkPackage $workPackage,
    ): bool {
        return $user->can('work-package.delete');
    }

    public function start(
        User $user,
        WorkPackage $workPackage,
    ): bool {
        return $user->can('work-package.start');
    }
}

