<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use LogicException;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Events\ChecklistActivated;
use Rimba\Work\Events\ChecklistCompleted;
use Rimba\Work\Events\TaskCancelled;
use Rimba\Work\Events\TaskClaimed;
use Rimba\Work\Events\TaskCompleted;
use Rimba\Work\Events\TaskReleased;
use Rimba\Work\Events\TaskSkipped;
use Rimba\Work\Events\WorkPackageCompleted;
use Rimba\Work\Events\WorkPackageStarted;
use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\ChecklistInstance;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\TaskInstance;
use Rimba\Work\Models\WorkPackage;
use Rimba\Work\Models\WorkPackageInstance;

final class ChecklistInstanceObserver
{
    public function creating(
        ChecklistInstance $instance,
    ): void {

        $instance->status ??=
            ChecklistStatus::Pending;

        $instance->activated_at ??=
            now();
    }

    public function created(
        ChecklistInstance $instance,
    ): void {

        event(
            new ChecklistActivated(
                $instance
            )
        );
    }

    public function updated(
        ChecklistInstance $instance,
    ): void {

        if (
            $instance->wasChanged('status')
            && $instance->status === ChecklistStatus::Completed
        ) {
            event(
                new ChecklistCompleted(
                    $instance
                )
            );
        }
    }
}

