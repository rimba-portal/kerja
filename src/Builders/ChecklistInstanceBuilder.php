<?php

declare(strict_types=1);

namespace Rimba\Work\Builders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;

final class ChecklistInstanceBuilder extends Builder
{
    public function pending(): static
    {
        return $this->where(
            'status',
            ChecklistStatus::Pending,
        );
    }

    public function completed(): static
    {
        return $this->where(
            'status',
            ChecklistStatus::Completed,
        );
    }

    public function cancelled(): static
    {
        return $this->where(
            'status',
            ChecklistStatus::Cancelled,
        );
    }

    public function forWorkPackageInstance(
        int $id,
    ): static {

        return $this->where(
            'work_package_instance_id',
            $id,
        );
    }
}
