<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\ExecutionStatus;
use Rimba\Work\Models\OfferingExecution;

class CompleteOfferingExecution
{
    public function execute(
        OfferingExecution $execution,
        ?Model $subject,
        ?Model $workflowRun,
        array $result = [],
        bool $workflowQueued = false,
    ): OfferingExecution {
        $workflowStarted = $workflowQueued
            || $workflowRun instanceof Model;

        $execution->forceFill([
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),

            'workflow_instance_type' => $workflowRun?->getMorphClass(),
            'workflow_instance_id' => $workflowRun?->getKey(),

            'status' => $workflowStarted
                ? ExecutionStatus::Running
                : ExecutionStatus::Completed,

            'result_snapshot' => $result,

            'completed_at' => $workflowStarted
                ? null
                : now(),
        ])->save();

        return $execution->refresh();
    }
}
