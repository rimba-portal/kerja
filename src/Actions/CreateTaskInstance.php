<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Core\Models\Role;
use App\Trees\Organization\Models\Staff;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Events\ChecklistActivated;
use Rimba\Work\Events\ChecklistCompleted;
use Rimba\Work\Events\TaskCancelled;
use Rimba\Work\Events\TaskClaimed;
use Rimba\Work\Events\TaskCompleted;
use Rimba\Work\Events\TaskReleased;
use Rimba\Work\Events\TaskSkipped;
use Rimba\Work\Events\WorkPackageCompleted;
use Rimba\Work\Events\WorkPackageStarted;
use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\TaskInstance;
use Rimba\Work\Models\WorkPackage;
use Rimba\Work\Models\WorkPackageInstance;

final class CreateTaskInstance
{
    public function execute(
        ChecklistInstance $checklistInstance,
        Task $task,
    ): TaskInstance {

        return TaskInstance::create([
            'work_package_instance_id'
            => $checklistInstance->work_package_instance_id,

            'checklist_instance_id'
            => $checklistInstance->id,

            'task_id'
            => $task->id,

            'status'
            => TaskStatus::Queue,
        ]);
    }
}

