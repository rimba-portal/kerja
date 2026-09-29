<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Models\Workflow;
use Rimba\Work\Models\WorkflowInstance;
use Rimba\Work\Services\WorkflowInitiatorService;
use RuntimeException;

final class StartWorkflow
{
    public function __construct(private WorkflowInitiatorService $workflowInitiatorService, private CreateTask $createTask) {}

    public function execute(Workflow $w, Model $i, array $payload): WorkflowInstance
    {
        if (! $this->workflowInitiatorService->mayStart($i, $w)) {
            throw new RuntimeException('Initiator not allowed.');
        }

        $step = $w->startStep() ?? throw new RuntimeException('No start step.');

        return DB::transaction(function () use ($w, $i, $payload, $step) {
            $workflowInstance = WorkflowInstance::query()->create(['workflow_id' => $w->id, 'workflow_version' => $w->version, 'initiator_type' => $i->getMorphClass(), 'initiator_id' => $i->getKey(), 'current_workflow_step_id' => $step->id, 'status' => 'active', 'payload' => $payload, 'started_at' => now()]);
            $this->createTask->execute($workflowInstance, $step);

            return $workflowInstance->fresh();
        });
    }
}
