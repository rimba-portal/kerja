<?php

declare(strict_types=1);

namespace Rimba\Work\Builders;

use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Enums\ChecklistStatus;

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
