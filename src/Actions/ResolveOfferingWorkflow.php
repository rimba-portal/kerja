<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Data\WorkflowDefinition;
use Rimba\Work\Models\Offering;
use Rimba\Work\Services\WorkflowManager;

class ResolveOfferingWorkflow
{
    public function __construct(
        protected WorkflowManager $workflows,
    ) {}

    public function execute(
        Offering $offering,
    ): ?WorkflowDefinition {
        if (! $offering->requiresWorkflow()) {
            return null;
        }

        return $this->workflows->resolve(
            $offering->workflow,
        );
    }
}
