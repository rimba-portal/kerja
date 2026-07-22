<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use Rimba\Work\Models\Checklist;

final class ChecklistObserver
{
    public function creating(
        Checklist $checklist,
    ): void {

        $checklist->sort_order ??= (
            (int) $checklist
                ->workPackage
                ?->checklists()
                ->max('sort_order')
        ) + 1;
    }
}
