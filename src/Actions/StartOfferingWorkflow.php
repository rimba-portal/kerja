<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Data\OfferingContext;
use Rimba\Work\Models\Offering;
use Rimba\Work\Models\OfferingExecution;
use Rimba\Work\Services\WorkflowManager;

class StartOfferingWorkflow
{
    public function __construct(
        protected WorkflowManager $workflows,
    ) {}

    public function execute(
        Offering $offering,
        OfferingExecution $execution,
        ?Model $subject,
        OfferingContext $context,
        ?bool $queue = null,
    ): ?Model {
        if (! $offering->requiresWorkflow()) {
            return null;
        }

        return $this->workflows->run(
            workflow: $offering->workflow,
            subject: $subject,
            payload: [
                'rimba' => [
                    'execution_id' => $execution->getKey(),
                ],

                'offering' => [
                    'id' => $offering->getKey(),
                    'code' => $offering->code,
                    'name' => $offering->name,
                ],

                'context' => $context->toArray(),
            ],
            queue: $queue,
        );
    }
}
