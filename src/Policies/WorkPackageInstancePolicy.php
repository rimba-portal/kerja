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

final class WorkPackageInstancePolicy
{
    public function view(
        User $user,
        WorkPackageInstance $instance,
    ): bool {
        return $user->can('work-package-instance.view');
    }

    public function start(
        User $user,
    ): bool {
        return $user->can('work-package-instance.start');
    }

    public function cancel(
        User $user,
        WorkPackageInstance $instance,
    ): bool {
        return $user->can('work-package-instance.cancel');
    }
}

