<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\WorkflowStatus;
use Rimba\Work\Models\Transition;
use Rimba\Work\Models\WorkflowInstance;

class FailWorkflow
{
    public function execute(
        WorkflowInstance $workflow,
        string $reason,
        ?Model $actor = null,
    ): WorkflowInstance {
        $workflow->update([
            'status' => WorkflowStatus::Failed,
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);

        Transition::query()->create([
            'workflow_instance_id' => $workflow->getKey(),
            'event' => 'workflow_failed',
            'actor_type' => $actor?->getMorphClass(),
            'actor_id' => $actor?->getKey(),
            'payload' => [
                'reason' => $reason,
            ],
            'performed_at' => now(),
        ]);

        return $workflow->fresh();
    }
}
