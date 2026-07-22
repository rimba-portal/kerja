<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Events\WorkPackageStarted;
use Rimba\Work\Models\WorkPackage;
use Rimba\Work\Models\WorkPackageInstance;

final class StartWorkPackage
{
    public function execute(
        WorkPackage $workPackage,
    ): WorkPackageInstance {

        $instance = WorkPackageInstance::create([
            'work_package_id' => $workPackage->id,
            'status' => WorkPackageStatus::Active,
            'started_at' => now(),
        ]);

        event(new WorkPackageStarted($instance));

        return $instance;
    }
}
