<?php

declare(strict_types=1);

namespace Rimba\Work\Builders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;

final class TaskInstanceBuilder extends Builder
{
    public function queue(): static
    {
        return $this->where(
            'status',
            TaskStatus::Queue,
        );
    }

    public function active(): static
    {
        return $this->where(
            'status',
            TaskStatus::Active,
        );
    }

    public function completed(): static
    {
        return $this->where(
            'status',
            TaskStatus::Completed,
        );
    }

    public function skipped(): static
    {
        return $this->where(
            'status',
            TaskStatus::Skipped,
        );
    }

    public function cancelled(): static
    {
        return $this->where(
            'status',
            TaskStatus::Cancelled,
        );
    }

    public function assigned(): static
    {
        return $this->whereNotNull(
            'assigned_to_id'
        );
    }

    public function unassigned(): static
    {
        return $this->whereNull(
            'assigned_to_id'
        );
    }

    public function assignedTo(
        int $staffId,
    ): static {

        return $this->where(
            'assigned_to_id',
            $staffId,
        );
    }

    public function completedBy(
        int $staffId,
    ): static {

        return $this->where(
            'completed_by_id',
            $staffId,
        );
    }

    public function claimableByRole(
        int $roleId,
    ): static {

        return $this->queue()
            ->whereHas(
                'task',
                fn ($query) => $query->where(
                    'role_id',
                    $roleId,
                )
            );
    }

    public function forChecklistInstance(
        int $id,
    ): static {

        return $this->where(
            'checklist_instance_id',
            $id,
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

    public function completedToday(): static
    {
        return $this->whereDate(
            'completed_at',
            today(),
        );
    }
}