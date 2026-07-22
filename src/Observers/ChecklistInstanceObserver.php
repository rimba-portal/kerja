<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Events\ChecklistActivated;
use Rimba\Work\Events\ChecklistCompleted;
use Rimba\Work\Models\ChecklistInstance;

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
