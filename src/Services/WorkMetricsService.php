<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\TaskInstance;
use Rimba\Work\Models\WorkPackage;
use Rimba\Work\Models\WorkPackageInstance;

final class WorkMetricsService
{
    public function activeWorkPackages(): int
    {
        return WorkPackageInstance::query()
            ->where(
                'status',
                WorkPackageStatus::Active
            )
            ->count();
    }

    public function completedWorkPackages(): int
    {
        return WorkPackageInstance::query()
            ->where(
                'status',
                WorkPackageStatus::Completed
            )
            ->count();
    }

    public function cancelledWorkPackages(): int
    {
        return WorkPackageInstance::query()
            ->where(
                'status',
                WorkPackageStatus::Cancelled
            )
            ->count();
    }

    public function pendingChecklists(): int
    {
        return ChecklistInstance::query()
            ->where(
                'status',
                ChecklistStatus::Pending
            )
            ->count();
    }

    public function queuedTasks(): int
    {
        return TaskInstance::query()
            ->where(
                'status',
                TaskStatus::Queue
            )
            ->count();
    }

    public function activeTasks(): int
    {
        return TaskInstance::query()
            ->where(
                'status',
                TaskStatus::Active
            )
            ->count();
    }

    public function completedTasks(): int
    {
        return TaskInstance::query()
            ->where(
                'status',
                TaskStatus::Completed
            )
            ->count();
    }

    public function completionRate(): float
    {
        $total = WorkPackageInstance::query()
            ->count();

        if ($total === 0) {
            return 0;
        }

        $completed = WorkPackageInstance::query()
            ->where(
                'status',
                WorkPackageStatus::Completed
            )
            ->count();

        return round(
            ($completed / $total) * 100,
            2,
        );
    }

    public function averageTasksPerWorkPackage(): float
    {
        $totalPackages = WorkPackage::query()->count();

        if ($totalPackages === 0) {
            return 0;
        }

        $totalTasks = Task::query()
            ->count();

        return round(
            $totalTasks / $totalPackages,
            2,
        );
    }
}
