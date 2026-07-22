<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use LogicException;
use Rimba\Work\Models\WorkPackage;

final class WorkPackageObserver
{
    public function creating(
        WorkPackage $workPackage,
    ): void {

        $workPackage->active ??= true;
    }

    public function deleting(
        WorkPackage $workPackage,
    ): void {

        if (
            $workPackage
                ->workPackageInstances()
                ->exists()
        ) {
            throw new LogicException(
                'Cannot delete WorkPackage with executions.'
            );
        }
    }
}
