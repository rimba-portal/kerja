<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\WorkflowAction;

final readonly class WorkflowActionSelected
{
    public function __construct(public Task $task, public WorkflowAction $workflowAction, public Model $actor) {}
}
