<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\People\Models\Staff;
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

        $staff = $user?->staff;

        if (! $staff instanceof Staff) {
            $staff = null;
        }

        $jobPosition = $staff?->jobPosition;

        return new OfferingContext(
            userId: $this->identifier(
                $user?->getAuthIdentifier(),
            ),

            staffId: $this->identifier(
                $staff?->getKey(),
            ),

            jobPositionId: $this->identifier(
                $jobPosition?->getKey(),
            ),

            orgTeamId: $this->identifier(
                $metadata['org_team_id']
                    ?? null,
            ),

            orgUnitId: $this->identifier(
                $staff?->org_unit_id,
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
