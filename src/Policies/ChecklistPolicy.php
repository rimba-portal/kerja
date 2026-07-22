<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use App\Models\User;
use Rimba\Work\Models\Checklist;

final class ChecklistPolicy
{
    public function viewAny(
        User $user,
    ): bool {
        return $user->can('checklist.viewAny');
    }

    public function create(
        User $user,
    ): bool {
        return $user->can('checklist.create');
    }

    public function update(
        User $user,
        Checklist $checklist,
    ): bool {
        return $user->can('checklist.update');
    }

    public function delete(
        User $user,
        Checklist $checklist,
    ): bool {
        return $user->can('checklist.delete');
    }
}
