<?php

declare(strict_types=1);

namespace Rimba\Work\Data;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\OfferingExecution;

final readonly class ExecutionResult
{
    public function __construct(
        public OfferingExecution $execution,
        public ?Model $subject = null,
        public ?Model $workflowRun = null,
        public ?Model $formSubmission = null,
        public bool $queued = false,
    ) {}

    public function hasSubject(): bool
    {
        return $this->subject instanceof Model;
    }

    public function hasWorkflowRun(): bool
    {
        return $this->workflowRun instanceof Model;
    }

    public function isQueued(): bool
    {
        return $this->queued;
    }

    public function toArray(): array
    {
        return [
            'execution_id' => $this->execution->getKey(),

            'subject' => $this->subject instanceof Model
                ? [
                    'type' => $this->subject->getMorphClass(),
                    'id' => $this->subject->getKey(),
                ]
                : null,

            'workflow_run' => $this->workflowRun instanceof Model
                ? [
                    'type' => $this->workflowRun->getMorphClass(),
                    'id' => $this->workflowRun->getKey(),
                ]
                : null,

            'form_submission' => $this->formSubmission instanceof Model
                ? [
                    'type' => $this->formSubmission->getMorphClass(),
                    'id' => $this->formSubmission->getKey(),
                ]
                : null,

            'queued' => $this->queued,
        ];
    }
}
