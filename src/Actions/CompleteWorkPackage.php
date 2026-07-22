<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Events\WorkPackageCompleted;
use Rimba\Work\Models\WorkPackageInstance;

final class CompleteWorkPackage
{
    public function execute(
        WorkPackageInstance $workPackageInstance,
    ): WorkPackageInstance {

        $workPackageInstance->update([
            'status' => WorkPackageStatus::Completed,
            'completed_at' => now(),
        ]);

        event(
            new WorkPackageCompleted(
                $workPackageInstance
            )
        );

        return $workPackageInstance->refresh();
    }
}
