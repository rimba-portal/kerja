<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Contracts\SubjectRegistry;
use Rimba\Work\Data\ExecutionResult;
use Rimba\Work\Models\Offering;

class WorkManager
{
    public function __construct(
        protected OfferingManager $offerings,
        protected FormManager $forms,
        protected WorkflowManager $workflows,
        protected SubjectRegistry $subjects,
    ) {}

    public function offerings(): OfferingManager
    {
        return $this->offerings;
    }

    public function forms(): FormManager
    {
        return $this->forms;
    }

    public function workflows(): WorkflowManager
    {
        return $this->workflows;
    }

    public function subjects(): SubjectRegistry
    {
        return $this->subjects;
    }

    public function execute(
        Offering|string $offering,
        array $input = [],
        array $metadata = [],
        ?Model $subject = null,
        ?Model $formSubmission = null,
        bool $alreadyValidated = false,
        ?bool $queue = null,
    ): ExecutionResult {
        return $this->offerings->execute(
            offering: $offering,
            input: $input,
            metadata: $metadata,
            subject: $subject,
            formSubmission: $formSubmission,
            alreadyValidated: $alreadyValidated,
            queue: $queue,
        );
    }
}
