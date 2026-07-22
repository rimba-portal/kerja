<?php

declare(strict_types=1);

namespace Rimba\Work\Listeners;

use Rimba\Trail\Models\AuditLog;
use Rimba\Work\Actions\ActivateChecklist;
use Rimba\Work\Actions\CompleteChecklist;
use Rimba\Work\Actions\CompleteWorkPackage;
use Rimba\Work\Actions\CreateTaskInstance;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Events\ChecklistActivated;
use Rimba\Work\Events\ChecklistCompleted;
use Rimba\Work\Events\TaskCancelled;
use Rimba\Work\Events\TaskCompleted;
use Rimba\Work\Events\TaskSkipped;
use Rimba\Work\Events\WorkPackageStarted;

final class AutoCompleteWorkPackage
{
    public function handle(
        ChecklistCompleted $event,
    ): void {

        $workPackageInstance =
            $event
                ->checklistInstance
                ->workPackageInstance;

        $activeChecklistExists =
            $workPackageInstance
                ->checklistInstances()
                ->where(
                    'status',
                    ChecklistStatus::Pending,
                )
                ->exists();

        if ($activeChecklistExists) {
            return;
        }

        $totalChecklists =
            $workPackageInstance
                ->workPackage
                ->checklists()
                ->count();

        $completedChecklists =
            $workPackageInstance
                ->checklistInstances()
                ->where(
                    'status',
                    ChecklistStatus::Completed,
                )
                ->count();

        if ($totalChecklists !== $completedChecklists) {
            return;
        }

        app(CompleteWorkPackage::class)
            ->execute(
                $workPackageInstance,
            );
    }
}

