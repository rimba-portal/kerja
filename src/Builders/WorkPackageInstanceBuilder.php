<?php

declare(strict_types=1);

namespace Rimba\Work\Builders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Enums\WorkPackageStatus;

final class WorkPackageInstanceBuilder extends Builder
{
    public function active(): static
    {
        return $this->where(
            'status',
            WorkPackageStatus::Active,
        );
    }

    public function completed(): static
    {
        return $this->where(
            'status',
            WorkPackageStatus::Completed,
        );
    }

    public function cancelled(): static
    {
        return $this->where(
            'status',
            WorkPackageStatus::Cancelled,
        );
    }

    public function startedBetween(
        Carbon $from,
        Carbon $to,
    ): static {

        return $this->whereBetween(
            'started_at',
            [$from, $to]
        );
    }

    public function completedBetween(
        Carbon $from,
        Carbon $to,
    ): static {

        return $this->whereBetween(
            'completed_at',
            [$from, $to]
        );
    }

    public function forWorkPackage(
        int $workPackageId,
    ): static {

        return $this->where(
            'work_package_id',
            $workPackageId,
        );
    }
}
