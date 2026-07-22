<?php

declare(strict_types=1);

namespace Rimba\Work\Listeners;

use Rimba\Work\Actions\CompleteWorkPackage;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Events\ChecklistCompleted;

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
