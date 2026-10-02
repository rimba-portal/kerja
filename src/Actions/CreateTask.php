<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\WorkflowInstance;
use Rimba\Work\Models\WorkflowStep;
use Rimba\Work\Services\ActorAssignmentService;

final class CreateTask
{
    public function __construct(private ActorAssignmentService $actorAssignmentService) {}

    public function execute(WorkflowInstance $x, WorkflowStep $s): Task
    {
        $a = $this->actorAssignmentService->resolve($s->workPackage);

        return $x->tasks()->create(['workflow_step_id' => $s->id, 'work_package_id' => $s->work_package_id, 'work_package_version' => $s->workPackage->version, 'assignee_type' => $a?->getMorphClass(), 'assignee_id' => $a?->getKey(), 'status' => $a instanceof Model ? 'assigned' : 'ready', 'payload' => $x->payload, 'assigned_at' => $a instanceof Model ? now() : null]);
    }
}
