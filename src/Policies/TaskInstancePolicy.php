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

final class TaskInstancePolicy
{
    public function view(
        User $user,
        TaskInstance $taskInstance,
    ): bool {

        if (
            $taskInstance->assigned_to_id
            === $user->staff?->id
        ) {
            return true;
        }

        return $user->can(
            'task-instance.view'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Claim
    |--------------------------------------------------------------------------
    |
    | Task must:
    | - be queue
    | - have matching role
    |
    */

    public function claim(
        User $user,
        TaskInstance $taskInstance,
    ): bool {

        if (
            $taskInstance->status
            !== TaskStatus::Queue
        ) {
            return false;
        }

        return $user
            ->staff
            ?->roles()
            ->whereKey(
                $taskInstance
                    ->task
                    ->role_id
            )
            ->exists()
            ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Release
    |--------------------------------------------------------------------------
    */

    public function release(
        User $user,
        TaskInstance $taskInstance,
    ): bool {

        return $taskInstance->assigned_to_id
            === $user->staff?->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Complete
    |--------------------------------------------------------------------------
    */

    public function complete(
        User $user,
        TaskInstance $taskInstance,
    ): bool {

        return $taskInstance->assigned_to_id
            === $user->staff?->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Skip
    |--------------------------------------------------------------------------
    */

    public function skip(
        User $user,
        TaskInstance $taskInstance,
    ): bool {

        return $user->can(
            'task-instance.skip'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel
    |--------------------------------------------------------------------------
    */

    public function cancel(
        User $user,
        TaskInstance $taskInstance,
    ): bool {

        return $user->can(
            'task-instance.cancel'
        );
    }
}

