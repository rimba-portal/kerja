<?php

declare(strict_types=1);

namespace Rimba\Work\Builders;

use Illuminate\Database\Eloquent\Builder;

final class ChecklistBuilder extends Builder
{
    public function ordered(): static
    {
        return $this->orderBy('sort_order');
    }

    public function forWorkPackage(
        int $workPackageId,
    ): static {

        return $this->where(
            'work_package_id',
            $workPackageId,
        );
    }

    public function withTasks(): static
    {
        return $this->with('tasks');
    }

    public function withTaskCount(): static
    {
        return $this->withCount('tasks');
    }
}
