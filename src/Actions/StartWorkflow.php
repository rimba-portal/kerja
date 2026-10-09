<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Models\WorkFlow;
use Rimba\Work\Models\WorkFlowInstance;
use Rimba\Work\Services\TaskEventLogger;
use Rimba\Work\Services\WorkflowInitiatorService;
use RuntimeException;

final class StartWorkflow
{
    public function __construct(private WorkflowInitiatorService $workflowInitiatorService, private CreateTask $createTask, private TaskEventLogger $taskEventLogger) {}

    public function execute(WorkFlow $workflow, Model $initiator, Model $subject, array $payload = []): WorkFlowInstance
    {
        if (! $this->workflowInitiatorService->mayStart($initiator, $workflow)) {
            throw new RuntimeException('Initiator not allowed.');
        }

        $step = $workflow->startStep() ?? throw new RuntimeException('No start step.');

        return DB::transaction(function () use ($workflow, $initiator, $subject, $payload, $step) {
            $workFlowInstance = WorkFlowInstance::query()->create([
                'workflow_id' => $workflow->id, 'workflow_version' => $workflow->version,
                'initiator_type' => $initiator->getMorphClass(), 'initiator_id' => $initiator->getKey(),
                'subject_type' => $subject->getMorphClass(), 'subject_id' => $subject->getKey(),
                'current_workflow_step_id' => $step->id, 'status' => 'active', 'payload' => $payload, 'started_at' => now(),
            ]);
            $task = $this->createTask->execute($workFlowInstance, $step);
            $this->taskEventLogger->log($workFlowInstance, 'workflow_started', $initiator, $task, null, ['subject_type' => $subject->getMorphClass(), 'subject_id' => $subject->getKey()]);

            return $workFlowInstance->fresh();
        });
    }
}
