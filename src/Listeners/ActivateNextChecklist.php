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

final class ActivateNextChecklist
{
    public function handle(
        ChecklistCompleted $event,
    ): void {

        $completedChecklistInstance =
            $event->checklistInstance;

        $workPackageInstance =
            $completedChecklistInstance
                ->workPackageInstance;

        $currentSortOrder =
            $completedChecklistInstance
                ->checklist
                ->sort_order;

        $nextChecklist =
            $workPackageInstance
                ->workPackage
                ->checklists()
                ->where(
                    'sort_order',
                    '>',
                    $currentSortOrder,
                )
                ->orderBy('sort_order')
                ->first();

        if (! $nextChecklist) {
            return;
        }

        app(ActivateChecklist::class)
            ->execute(
                $workPackageInstance,
                $nextChecklist,
            );
    }
}

