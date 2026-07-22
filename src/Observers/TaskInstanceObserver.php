<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Events\TaskCancelled;
use Rimba\Work\Events\TaskClaimed;
use Rimba\Work\Events\TaskCompleted;
use Rimba\Work\Events\TaskReleased;
use Rimba\Work\Events\TaskSkipped;
use Rimba\Work\Models\TaskInstance;

final class TaskInstanceObserver
{
    public function creating(
        TaskInstance $instance,
    ): void {

        $instance->status ??=
            TaskStatus::Queue;
    }

    public function updated(
        TaskInstance $instance,
    ): void {

        /*
        |----------------------------------------------------------
        | Claimed
        |----------------------------------------------------------
        */

        if (
            $instance->wasChanged('assigned_to_id')
            && $instance->assigned_to_id
            && $instance->status === TaskStatus::Active
        ) {
            event(
                new TaskClaimed(
                    $instance
                )
            );
        }

        /*
        |----------------------------------------------------------
        | Released
        |----------------------------------------------------------
        */

        if (
            $instance->wasChanged('assigned_to_id')
            && $instance->assigned_to_id === null
            && $instance->status === TaskStatus::Queue
        ) {
            event(
                new TaskReleased(
                    $instance
                )
            );
        }

        /*
        |----------------------------------------------------------
        | Completed
        |----------------------------------------------------------
        */

        if (
            $instance->wasChanged('status')
            && $instance->status === TaskStatus::Completed
        ) {
            event(
                new TaskCompleted(
                    $instance
                )
            );
        }

        /*
        |----------------------------------------------------------
        | Skipped
        |----------------------------------------------------------
        */

        if (
            $instance->wasChanged('status')
            && $instance->status === TaskStatus::Skipped
        ) {
            event(
                new TaskSkipped(
                    $instance
                )
            );
        }

        /*
        |----------------------------------------------------------
        | Cancelled
        |----------------------------------------------------------
        */

        if (
            $instance->wasChanged('status')
            && $instance->status === TaskStatus::Cancelled
        ) {
            event(
                new TaskCancelled(
                    $instance
                )
            );
        }
    }
}
