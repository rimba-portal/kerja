<?php

declare(strict_types=1);

namespace Rimba\Work\Builders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;

final class WorkPackageBuilder extends Builder
{
    public function active(): static
    {
        return $this->where('active', true);
    }

    public function inactive(): static
    {
        return $this->where('active', false);
    }

    public function withChecklists(): static
    {
        return $this->with('checklists');
    }

    public function withChecklistCount(): static
    {
        return $this->withCount('checklists');
    }

    public function withInstances(): static
    {
        return $this->with('workPackageInstances');
    }
}
