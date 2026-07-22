<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use App\Trees\Organization\Models\Staff;
use Rimba\Work\Actions\ActivateChecklist;
use Rimba\Work\Actions\AssignTask;
use Rimba\Work\Actions\ClaimTask;
use Rimba\Work\Actions\CompleteChecklist;
use Rimba\Work\Actions\CompleteWorkPackage;
use Rimba\Work\Actions\ReleaseTask;
use Rimba\Work\Actions\StartWorkPackage;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\TaskInstance;
use Rimba\Work\Models\WorkPackage;
use Rimba\Work\Models\WorkPackageInstance;


final class ChecklistProgressService
{
    public function totalTasks(
        ChecklistInstance $instance,
    ): int {

        return $instance
            ->taskInstances()
            ->count();
    }

    public function completedTasks(
        ChecklistInstance $instance,
    ): int {

        return $instance
            ->taskInstances()
            ->whereIn('status', [
                TaskStatus::Completed,
                TaskStatus::Skipped,
                TaskStatus::Cancelled,
            ])
            ->count();
    }

    public function remainingTasks(
        ChecklistInstance $instance,
    ): int {

        return $instance
            ->taskInstances()
            ->whereIn('status', [
                TaskStatus::Queue,
                TaskStatus::Active,
            ])
            ->count();
    }

    public function percentage(
        ChecklistInstance $instance,
    ): float {

        $total = $this->totalTasks($instance);

        if ($total === 0) {
            return 0;
        }

        return round(
            ($this->completedTasks($instance) / $total) * 100,
            2,
        );
    }

    public function isComplete(
        ChecklistInstance $instance,
    ): bool {

        return $this->remainingTasks(
            $instance
        ) === 0;
    }
}

