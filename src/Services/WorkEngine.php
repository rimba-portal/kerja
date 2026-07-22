<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use App\Trees\Organization\Models\Staff;
use Rimba\Work\Actions\ActivateChecklist;
use Rimba\Work\Actions\AssignTask;
use Rimba\Work\Actions\ClaimTask;
use Rimba\Work\Actions\CompleteChecklist;
use Rimba\Work\Actions\CompleteWorkPackage;
use Rimba\Work\Actions\ReleaseTask;
use Rimba\Work\Actions\StartWorkPackage;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\TaskInstance;
use Rimba\Work\Models\WorkPackage;
use Rimba\Work\Models\WorkPackageInstance;


final class WorkEngine
{
    public function start(
        WorkPackage $workPackage,
    ): WorkPackageInstance {

        return app(StartWorkPackage::class)
            ->execute($workPackage);
    }

    public function activateChecklist(
        WorkPackageInstance $instance,
        Checklist $checklist,
    ): ChecklistInstance {

        return app(ActivateChecklist::class)
            ->execute(
                $instance,
                $checklist,
            );
    }

    public function completeChecklist(
        ChecklistInstance $instance,
    ): ChecklistInstance {

        return app(CompleteChecklist::class)
            ->execute($instance);
    }

    public function completeWorkPackage(
        WorkPackageInstance $instance,
    ): WorkPackageInstance {

        return app(CompleteWorkPackage::class)
            ->execute($instance);
    }

    public function nextChecklist(
        ChecklistInstance $instance,
    ): ?Checklist {

        return $instance
            ->workPackageInstance
            ->workPackage
            ->checklists()
            ->where(
                'sort_order',
                '>',
                $instance->checklist->sort_order
            )
            ->orderBy('sort_order')
            ->first();
    }
}

