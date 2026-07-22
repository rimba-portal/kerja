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

final class CreateFirstChecklist
{
    public function handle(
        WorkPackageStarted $event,
    ): void {

        $workPackageInstance =
            $event->workPackageInstance;

        $firstChecklist = $workPackageInstance
            ->workPackage
            ->checklists()
            ->orderBy('sort_order')
            ->first();

        if (! $firstChecklist) {
            return;
        }

        app(ActivateChecklist::class)
            ->execute(
                $workPackageInstance,
                $firstChecklist,
            );
    }
}

