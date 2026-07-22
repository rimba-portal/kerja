<?php

declare(strict_types=1);

namespace Rimba\Work\Builders;

use Illuminate\Database\Eloquent\Builder;

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
