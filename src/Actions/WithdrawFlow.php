<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\FlowInstanceStatus;
use Rimba\Work\Models\FlowInstance;
use Rimba\Work\Services\TaskEventLogger;
use RuntimeException;

final class WithdrawFlow
{
    public function __construct(
        private TaskEventLogger $taskEventLogger,
    ) {}

    public function execute(
        FlowInstance $instance,
        Model $requestor,
    ): FlowInstance {
        if ($instance->status !== FlowInstanceStatus::Active) {
            throw new RuntimeException('Only an active workflow may be withdrawn.');
        }

        if (! $instance->initiator?->is($requestor)) {
            throw new RuntimeException(
                'Only the workflow requestor may withdraw the request.'
            );
        }

        return DB::transaction(function () use (
            $instance,
            $requestor,
        ): FlowInstance {
            $instance->tasks()
                ->whereIn('status', ['ready', 'assigned', 'started'])
                ->update([
                    'status' => 'cancelled',
                ]);

            $instance->update([
                'status' => FlowInstanceStatus::Withdrawn,
                'current_workflow_step_id' => null,
                'withdrawn_at' => now(),
            ]);

            $this->taskEventLogger->log(
                $instance,
                'workflow_withdrawn',
                $requestor,
            );

            return $instance->fresh();
        });
    }
}
