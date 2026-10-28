<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use Rimba\Work\Enums\ExecutionStatus;
use Rimba\Work\Models\OfferingExecution;

class OfferingExecutionObserver
{
    public function creating(
        OfferingExecution $execution,
    ): void {
        $execution->status ??=
            ExecutionStatus::Pending;
    }

    public function updating(
        OfferingExecution $execution,
    ): void {
        if (! $execution->isDirty('status')) {
            return;
        }

        match ($execution->status) {
            ExecutionStatus::Running => $this->running(
                $execution,
            ),

            ExecutionStatus::Completed => $this->completed(
                $execution,
            ),

            ExecutionStatus::Failed => $this->failed(
                $execution,
            ),

            ExecutionStatus::Cancelled => $this->cancelled(
                $execution,
            ),

            default => null,
        };
    }

    protected function running(
        OfferingExecution $execution,
    ): void {
        $execution->started_at ??= now();
    }

    protected function completed(
        OfferingExecution $execution,
    ): void {
        $execution->completed_at ??= now();
    }

    protected function failed(
        OfferingExecution $execution,
    ): void {
        $execution->failed_at ??= now();
    }

    protected function cancelled(
        OfferingExecution $execution,
    ): void {
        $execution->cancelled_at ??= now();
    }
}
