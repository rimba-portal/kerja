<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use App\Trees\Organization\Models\Staff;
use Rimba\Work\Actions\ActivateChecklist;
use Rimba\Work\Actions\AssignTask;
use Rimba\Work\Actions\ClaimTask;
use Rimba\Work\Actions\CompleteChecklist;
use Rimba\Work\Actions\CompleteWorkPackage;
use Rimba\Work\Actions\ReleaseTask;
use Rimba\Work\Actions\StartWorkPackage;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\TaskInstance;
use Rimba\Work\Models\WorkPackage;
use Rimba\Work\Models\WorkPackageInstance;


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

