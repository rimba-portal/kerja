<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Events\TaskCancelled;
use Rimba\Work\Models\TaskInstance;

final class CancelTask
{
    public function execute(
        TaskInstance $taskInstance,
    ): TaskInstance {

        $taskInstance->update([
            'status' => TaskStatus::Cancelled,
        ]);

        event(
            new TaskCancelled(
                $taskInstance
            )
        );

        return $taskInstance->refresh();
    }
}
