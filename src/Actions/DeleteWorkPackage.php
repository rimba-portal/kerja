<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\WorkPackage;

final class DeleteWorkPackage
{
    public function execute(
        WorkPackage $workPackage,
    ): void {
        $workPackage->delete();
    }
}
