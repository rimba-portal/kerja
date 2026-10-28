<?php

declare(strict_types=1);

namespace Rimba\Work\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rimba\Work\Enums\ExecutionStatus;
use Rimba\Work\Models\OfferingExecution;

trait HasWorkflow
{
    public function workExecutions(): MorphMany
    {
        return $this->morphMany(
            OfferingExecution::class,
            'subject',
        );
    }

    public function activeWorkExecutions(): MorphMany
    {
        return $this->workExecutions()
            ->whereIn(
                'status',
                [
                    ExecutionStatus::Pending->value,
                    ExecutionStatus::Preparing->value,
                    ExecutionStatus::Running->value,
                ],
            );
    }

    public function latestWorkExecution(): ?OfferingExecution
    {
        return $this
            ->workExecutions()
            ->latest('created_at')
            ->first();
    }

    public function currentWorkflowRun(): ?object
    {
        return $this
            ->latestWorkExecution()
            ?->workflowInstance;
    }

    public function hasActiveWorkflow(): bool
    {
        return $this
            ->activeWorkExecutions()
            ->exists();
    }
}
