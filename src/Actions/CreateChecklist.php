<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\WorkPackage;

final class CreateChecklist
{
    public function execute(
        WorkPackage $workPackage,
        string $name,
        int $sortOrder = 0,
    ): Checklist {

        return $workPackage
            ->checklists()
            ->create([
                'name' => $name,
                'sort_order' => $sortOrder,
            ]);
    }
}
