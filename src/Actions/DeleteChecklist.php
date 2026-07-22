<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\Checklist;

final class DeleteChecklist
{
    public function execute(
        Checklist $checklist,
    ): void {

        $checklist->delete();
    }
}
