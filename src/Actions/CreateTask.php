<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\FlowInstance;
use Rimba\Work\Models\FlowStep;
use Rimba\Work\Models\Task;

final class CreateTask
{
    public function execute(FlowInstance $instance, FlowStep $step): Task
    {
        return $instance->tasks()->create([
            'workflow_step_id' => $step->id,
            'work_package_id' => $step->work_package_id,
            'work_package_version' => $step->package->version,
            'assignee_type' => null,
            'assignee_id' => null,
            'status' => TaskStatus::Ready,
            'payload' => null,
            'assigned_at' => null,
        ]);
    }
}
