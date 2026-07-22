<?php

declare(strict_types=1);

namespace Rimba\Work\Listeners;

use Rimba\Work\Actions\ActivateChecklist;
use Rimba\Work\Events\ChecklistCompleted;

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
