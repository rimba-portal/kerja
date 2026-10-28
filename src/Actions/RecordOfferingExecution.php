<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Data\MappedOfferingData;
use Rimba\Work\Data\OfferingContext;
use Rimba\Work\Enums\ExecutionStatus;
use Rimba\Work\Models\Offering;
use Rimba\Work\Models\OfferingExecution;

class RecordOfferingExecution
{
    public function execute(
        Offering $offering,
        array $input,
        OfferingContext $context,
        MappedOfferingData $mapped,
        ?Model $formSubmission = null,
    ): OfferingExecution {
        return OfferingExecution::query()->create([
            'offering_code' => $offering->code,

            'user_id' => $context->userId,
            'org_team_id' => $context->orgTeamId,

            'form_submission_type' => $formSubmission?->getMorphClass(),
            'form_submission_id' => $formSubmission?->getKey(),

            'status' => ExecutionStatus::Preparing,

            'input_snapshot' => config(
                'work.execution.store_input_snapshot',
                true,
            )
                ? $input
                : null,

            'context_snapshot' => config(
                'work.execution.store_context_snapshot',
                true,
            )
                ? $context->toArray()
                : null,

            'mapped_snapshot' => config(
                'work.execution.store_mapped_snapshot',
                true,
            )
                ? $mapped->attributes
                : null,
        ]);
    }
}
