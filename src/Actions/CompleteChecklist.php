<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Core\Models\Role;
use App\Trees\Organization\Models\Staff;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Events\ChecklistActivated;
use Rimba\Work\Events\ChecklistCompleted;
use Rimba\Work\Events\TaskCancelled;
use Rimba\Work\Events\TaskClaimed;
use Rimba\Work\Events\TaskCompleted;
use Rimba\Work\Events\TaskReleased;
use Rimba\Work\Events\TaskSkipped;
use Rimba\Work\Events\WorkPackageCompleted;
use Rimba\Work\Events\WorkPackageStarted;
use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\TaskInstance;
use Rimba\Work\Models\WorkPackage;
use Rimba\Work\Models\WorkPackageInstance;

final class CompleteChecklist
{
    public function execute(
        ChecklistInstance $checklistInstance,
    ): ChecklistInstance {

        $checklistInstance->update([
            'status' => ChecklistStatus::Completed,
            'completed_at' => now(),
        ]);

        event(
            new ChecklistCompleted(
                $checklistInstance
            )
        );

        return $checklistInstance->refresh();
    }
}

