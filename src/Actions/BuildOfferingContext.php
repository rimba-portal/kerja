<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Contracts\OfferingContextBuilder;
use Rimba\Work\Data\OfferingContext;
use Rimba\Work\Models\Offering;

class BuildOfferingContext implements OfferingContextBuilder
{
    public function build(
        Offering $offering,
        array $metadata = [],
    ): OfferingContext {
        $user = auth()->user();

        return new OfferingContext(
            userId: $this->identifier(
                data_get($user, 'id'),
            ),

            staffId: $this->identifier(
                data_get($user, 'staff_id'),
            ),

            jobPositionId: $this->identifier(
                data_get($user, 'job_position_id'),
            ),

            orgTeamId: $this->identifier(
                $offering->org_team_id
                    ?? data_get($user, 'org_team_id'),
            ),

            orgUnitId: $this->identifier(
                data_get($user, 'org_unit_id'),
            ),

            offeringCode: $offering->code,

            source: (string) (
                $metadata['source']
                ?? config(
                    'work.execution.default_source',
                    'filament',
                )
            ),

            metadata: $metadata,
        );
    }

    protected function identifier(
        mixed $value,
    ): ?string {
        return filled($value)
            ? (string) $value
            : null;
    }
}
