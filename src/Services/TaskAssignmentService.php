<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use App\Trees\Organization\Models\Staff;
use Rimba\Work\Actions\AssignTask;
use Rimba\Work\Actions\ClaimTask;
use Rimba\Work\Actions\ReleaseTask;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\TaskInstance;

final class TaskAssignmentService
{
    public function assign(
        TaskInstance $taskInstance,
        Staff $staff,
    ): TaskInstance {

        return app(AssignTask::class)
            ->execute(
                $taskInstance,
                $staff,
            );
    }

    public function claim(
        TaskInstance $taskInstance,
        Staff $staff,
    ): TaskInstance {

        return app(ClaimTask::class)
            ->execute(
                $taskInstance,
                $staff,
            );
    }

    public function release(
        TaskInstance $taskInstance,
    ): TaskInstance {

        return app(ReleaseTask::class)
            ->execute($taskInstance);
    }

    public function isAssigned(
        TaskInstance $taskInstance,
    ): bool {

        return ! is_null(
            $taskInstance->assigned_to_id
        );
    }

    public function isClaimable(
        TaskInstance $taskInstance,
        Staff $staff,
    ): bool {

        if (
            $taskInstance->status !== TaskStatus::Queue
        ) {
            return false;
        }

        return $staff
            ->roles()
            ->whereKey(
                $taskInstance
                    ->task
                    ->role_id
            )
            ->exists();
    }
}
