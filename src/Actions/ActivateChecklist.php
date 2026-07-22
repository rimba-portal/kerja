<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Events\ChecklistActivated;
use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\WorkPackageInstance;

final class ActivateChecklist
{
    public function execute(
        WorkPackageInstance $instance,
        Checklist $checklist,
    ): ChecklistInstance {

        $checklistInstance = ChecklistInstance::create([
            'work_package_instance_id' => $instance->id,
            'checklist_id' => $checklist->id,
            'status' => ChecklistStatus::Pending,
            'activated_at' => now(),
        ]);

        event(
            new ChecklistActivated(
                $checklistInstance
            )
        );

        return $checklistInstance;
    }
}
