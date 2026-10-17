<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\FlowInstanceStatus;
use Rimba\Work\Enums\HookPoint;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\FlowAction;
use Rimba\Work\Models\FlowInstance;
use Rimba\Work\Models\Task;
use Rimba\Work\Services\HookBindingRunner;
use Rimba\Work\Services\LifecycleService;
use Rimba\Work\Services\TaskEventLogger;
use Rimba\Work\Support\HookContext;
use RuntimeException;

final class ExecuteFlowAction
{
    public function __construct(
        private CreateTask $createTask,
        private TaskEventLogger $taskEventLogger,
        private LifecycleService $lifecycleService,
        private HookBindingRunner $hookBindingRunner,
    ) {}

    public function execute(
        Task $task,
        FlowAction $action,
        Model $actor,
        array $formData = [],
        array $result = [],
    ): FlowInstance {
        if ($action->workflow_step_id !== $task->workflow_step_id) {
            throw new RuntimeException('FlowAction does not belong to the Task FlowStep.');
        }

        if (! $task->flowStep->step_type->allows($action->action_type)) {
            throw new RuntimeException('FlowAction is not allowed for this StepType.');
        }

        if (! $task->assignee?->is($actor)) {
            throw new RuntimeException('Only the Task assignee may execute the FlowAction.');
        }

        if (! in_array($task->status, [TaskStatus::Assigned, TaskStatus::Started], true)) {
            throw new RuntimeException('Task must be assigned or started before executing an action.');
        }

        return DB::transaction(function () use ($task, $action, $actor, $formData, $result): FlowInstance {
            $instance = $task->flowInstance;
            $step = $task->flowStep;

            if ($formData !== []) {
                $instance->form_data = array_replace_recursive($instance->form_data ?? [], $formData);
                $instance->save();
            }

            $hookContext = new HookContext(instance: $instance, task: $task, step: $step, action: $action, actor: $actor);
            $this->hookBindingRunner->runFor($step, HookPoint::StepExit, $hookContext);
            $this->hookBindingRunner->runFor($action, HookPoint::ActionExit, $hookContext);

            $task->update([
                'status' => TaskStatus::Completed,
                'result' => $result,
                'completed_at' => now(),
            ]);

            $this->taskEventLogger->log(
                $instance,
                'workflow_action_selected',
                $actor,
                $task,
                $action,
                ['action_type' => $action->action_type->value, 'result' => $result],
            );

            if ($action->target_workflow_step_id !== null) {
                $target = $action->targetFlowStep ?? throw new RuntimeException('Target FlowStep not found.');
                $instance->update(['current_workflow_step_id' => $target->id]);
                $this->hookBindingRunner->runFor(
                    $target,
                    HookPoint::StepEnter,
                    new HookContext(instance: $instance, step: $target, actor: $actor)
                );
                $this->createTask->execute($instance, $target);

                return $instance->fresh();
            }

            $terminalStatus = $action->terminal_status
                ?? throw new RuntimeException('Terminal FlowAction requires terminal_status.');

            if ($terminalStatus === FlowInstanceStatus::Active) {
                throw new RuntimeException('Active cannot be a terminal status.');
            }

            $updates = ['status' => $terminalStatus, 'current_workflow_step_id' => null];
            $timestamp = match ($terminalStatus) {
                FlowInstanceStatus::Completed => 'completed_at',
                FlowInstanceStatus::Rejected => 'rejected_at',
                FlowInstanceStatus::Cancelled => 'cancelled_at',
                FlowInstanceStatus::Withdrawn => 'withdrawn_at',
                FlowInstanceStatus::Failed => 'failed_at',
                FlowInstanceStatus::Active => null,
            };
            if ($timestamp) {
                $updates[$timestamp] = now();
            }

            $instance->update($updates);

            if ($terminalStatus === FlowInstanceStatus::Completed) {
                $this->lifecycleService->transition($instance);
            }

            $this->taskEventLogger->log($instance, 'workflow_'.$terminalStatus->value, $actor, $task, $action);

            return $instance->fresh();
        });
    }
}
