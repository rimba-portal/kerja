<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkflowStatus;
use Rimba\Work\Models\Transition;
use Rimba\Work\Models\WorkflowInstance;
use RuntimeException;

class CancelWorkflow
{
    public function execute(
        WorkflowInstance $workflow,
        ?Model $actor = null,
        ?string $reason = null,
    ): WorkflowInstance {
        return DB::transaction(function () use (
            $workflow,
            $actor,
            $reason,
        ): WorkflowInstance {
            $workflow = WorkflowInstance::query()
                ->lockForUpdate()
                ->findOrFail($workflow->getKey());

            if (
                in_array(
                    $workflow->status,
                    [
                        WorkflowStatus::Completed,
                        WorkflowStatus::Cancelled,
                    ],
                    true,
                )
            ) {
                throw new RuntimeException(
                    "Workflow [{$workflow->uuid}] cannot be cancelled from status [{$workflow->status->value}]."
                );
            }

            $cancellationReason = filled($reason)
                ? $reason
                : 'Workflow cancelled.';

            $cancelledAt = now();

            $workflow->tasks()
                ->whereIn('status', [
                    TaskStatus::Pending,
                    TaskStatus::Ready,
                    TaskStatus::Assigned,
                    TaskStatus::Started,
                    TaskStatus::Waiting,
                    TaskStatus::Failed,
                ])
                ->update([
                    'status' =>
                        TaskStatus::Cancelled->value,

                    'cancelled_at' =>
                        $cancelledAt,

                    'failure_reason' =>
                        $cancellationReason,
                ]);

            $workflow->update([
                'status' =>
                    WorkflowStatus::Cancelled,

                'current_workpackage_slug' =>
                    null,

                'cancelled_at' =>
                    $cancelledAt,

                'failure_reason' =>
                    $cancellationReason,
            ]);

            Transition::query()->create([
                'workflow_instance_id' =>
                    $workflow->getKey(),

                'event' =>
                    'workflow_cancelled',

                'actor_type' =>
                    $actor?->getMorphClass(),

                'actor_id' =>
                    $actor?->getKey(),

                'payload' => [
                    'reason' =>
                        $cancellationReason,
                ],

                'performed_at' =>
                    $cancelledAt,
            ]);

            return $workflow->fresh();
        });
    }
}