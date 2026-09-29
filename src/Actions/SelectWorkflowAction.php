<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\WorkflowAction;
use Rimba\Work\Models\WorkflowInstance;
use RuntimeException;

final class SelectWorkflowAction
{
    public function __construct(private CreateTask $createTask) {}

    public function execute(Task $t, WorkflowAction $a, Model $actor, array $result = []): WorkflowInstance
    {
        return DB::transaction(function () use ($t, $a, $result) {
            $t->update(['status' => 'completed', 'result' => $result, 'completed_at' => now()]);
            $x = $t->workflowInstance;
            if ($a->completion_effect === 'complete') {
                $x->update(['status' => 'completed', 'current_workflow_step_id' => null, 'completed_at' => now()]);
            } elseif ($a->completion_effect === 'cancel') {
                $x->update(['status' => 'cancelled', 'current_workflow_step_id' => null, 'cancelled_at' => now()]);
            } else {
                $target = $a->targetWorkflowStep ?? throw new RuntimeException('Target step required.');
                $x->update(['current_workflow_step_id' => $target->id]);
                $this->createTask->execute($x, $target);
            }

            return $x->fresh();
        });
    }
}
