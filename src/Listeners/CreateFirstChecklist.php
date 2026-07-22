<?php

declare(strict_types=1);

namespace Rimba\Work\Listeners;

use Rimba\Work\Actions\ActivateChecklist;
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
