<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use App\Models\User;
use Rimba\Work\Models\Task;

final class TaskPolicy
{
    public function viewAny(
        User $user,
    ): bool {
        return $user->can('task.viewAny');
    }

    public function create(
        User $user,
    ): bool {
        return $user->can('task.create');
    }

    public function update(
        User $user,
        Task $task,
    ): bool {
        return $user->can('task.update');
    }

    public function delete(
        User $user,
        Task $task,
    ): bool {
        return $user->can('task.delete');
    }
}
