<?php

declare(strict_types=1);

namespace Rimba\Work\Builders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;

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
