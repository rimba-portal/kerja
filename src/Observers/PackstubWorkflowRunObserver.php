<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\OfferingExecution;
use Rimba\Work\Services\WorkflowRunSynchronizer;

class PackstubWorkflowRunObserver
{
    public function __construct(
        protected WorkflowRunSynchronizer $synchronizer,
    ) {}

    public function created(
        Model $workflowRun,
    ): void {
        $this->synchronize(
            $workflowRun,
        );
    }

    public function updated(
        Model $workflowRun,
    ): void {
        if (
            method_exists(
                $workflowRun,
                'wasChanged',
            )
            && ! $workflowRun->wasChanged('status')
        ) {
            return;
        }

        $this->synchronize(
            $workflowRun,
        );
    }

    protected function synchronize(
        Model $workflowRun,
    ): void {
        $execution = $this->resolveExecution(
            $workflowRun,
        );

        if (! $execution instanceof OfferingExecution) {
            return;
        }

        $this->synchronizer->synchronize(
            execution: $execution,
            workflowRun: $workflowRun,
        );
    }

    protected function resolveExecution(
        Model $workflowRun,
    ): ?OfferingExecution {
        /*
         * Normal synchronous path:
         *
         * OfferingExecution already knows its workflow run.
         */
        $execution = OfferingExecution::query()
            ->where(
                'workflow_instance_type',
                $workflowRun->getMorphClass(),
            )
            ->where(
                'workflow_instance_id',
                $workflowRun->getKey(),
            )
            ->first();

        if ($execution) {
            return $execution;
        }

        /*
         * Queued workflow path:
         *
         * RIMBA placed execution_id into the Packstub payload before the
         * workflow run existed.
         */
        $executionId = data_get(
            $workflowRun,
            'payload.rimba.execution_id',
        );

        if (! $executionId) {
            return null;
        }

        $execution = OfferingExecution::query()
            ->find($executionId);

        if (! $execution) {
            return null;
        }

        /*
         * Establish the permanent relationship so subsequent status updates
         * no longer have to inspect the payload.
         */
        $execution->forceFill([
            'workflow_instance_type' => $workflowRun->getMorphClass(),

            'workflow_instance_id' => $workflowRun->getKey(),
        ])->save();

        return $execution;
    }
}
