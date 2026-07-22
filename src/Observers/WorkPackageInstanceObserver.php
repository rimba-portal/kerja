<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use Rimba\Work\Enums\WorkPackageStatus;
use Rimba\Work\Events\WorkPackageCompleted;
use Rimba\Work\Events\WorkPackageStarted;
use Rimba\Work\Models\WorkPackageInstance;

final class WorkPackageInstanceObserver
{
    public function creating(
        WorkPackageInstance $instance,
    ): void {

        $instance->status ??=
            WorkPackageStatus::Active;

        $instance->started_at ??=
            now();
    }

    public function created(
        WorkPackageInstance $instance,
    ): void {

        event(
            new WorkPackageStarted(
                $instance
            )
        );
    }

    public function updated(
        WorkPackageInstance $instance,
    ): void {

        if (
            $instance->wasChanged('status')
            && $instance->status === WorkPackageStatus::Completed
        ) {
            event(
                new WorkPackageCompleted(
                    $instance
                )
            );
        }
    }
}
