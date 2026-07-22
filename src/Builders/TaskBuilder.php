<?php

declare(strict_types=1);

namespace Rimba\Work\Builders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;

final class TaskBuilder extends Builder
{
    public function active(): static
    {
        return $this->where('active', true);
    }

    public function inactive(): static
    {
        return $this->where('active', false);
    }

    public function forChecklist(
        int $checklistId,
    ): static {

        return $this->where(
            'checklist_id',
            $checklistId,
        );
    }

    public function forRole(
        int $roleId,
    ): static {

        return $this->where(
            'role_id',
            $roleId,
        );
    }

    public function withRole(): static
    {
        return $this->with('role');
    }
}
