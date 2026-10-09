<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\WorkFlowAction;
use Rimba\Work\Models\WorkFlowInstance;
use Rimba\Work\Services\LifecycleService;
use Rimba\Work\Services\TaskEventLogger;
use RuntimeException;

final class SelectWorkflowAction
{
    public function __construct(private CreateTask $createTask, private TaskEventLogger $taskEventLogger, private LifecycleService $lifecycleService) {}

    public function execute(Task $task, WorkFlowAction $action, Model $actor, array $result = []): WorkFlowInstance
    {
        return DB::transaction(function () use ($task, $action, $actor, $result) {
            $task->update(['status' => 'completed', 'result' => $result, 'completed_at' => now()]);
            $instance = $task->workflowInstance;
            $this->taskEventLogger->log($instance, 'workflow_action_selected', $actor, $task, $action, $result);
            if ($action->completion_effect === 'complete') {
                $instance->update(['status' => 'completed', 'current_workflow_step_id' => null, 'completed_at' => now()]);
                $this->lifecycleService->transition($instance);
                $this->taskEventLogger->log($instance, 'workflow_completed', $actor, $task, $action);
            } elseif ($action->completion_effect === 'cancel') {
                $instance->update(['status' => 'cancelled', 'current_workflow_step_id' => null, 'cancelled_at' => now()]);
                $this->taskEventLogger->log($instance, 'workflow_cancelled', $actor, $task, $action);
            } else {
                $target = $action->targetWorkFlowStep ?? throw new RuntimeException('Target step required.');
                $instance->update(['current_workflow_step_id' => $target->id]);
                $this->createTask->execute($instance, $target);
            }

            return $instance->fresh();
        });
    }
}
