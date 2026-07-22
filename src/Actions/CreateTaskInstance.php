<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\TaskInstance;

final class CreateTaskInstance
{
    public function execute(
        ChecklistInstance $checklistInstance,
        Task $task,
    ): TaskInstance {

        return TaskInstance::create([
            'work_package_instance_id' => $checklistInstance->work_package_instance_id,

            'checklist_instance_id' => $checklistInstance->id,

            'task_id' => $task->id,

            'status' => TaskStatus::Queue,
        ]);
    }
}
