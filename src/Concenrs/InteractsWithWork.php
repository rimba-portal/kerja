<?php

declare(strict_types=1);

namespace Rimba\Work\Concerns;

trait InteractsWithWork
{
    use HasWorkflow;

    public function isWorkInProgress(): bool
    {
        return $this->hasActiveWorkflow();
    }

    public function latestWorkStatus(): ?string
    {
        return $this
            ->latestWorkExecution()
            ?->status
            ?->value;
    }
}
