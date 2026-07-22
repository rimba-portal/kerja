<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Events\TaskReleased;
use Rimba\Work\Models\TaskInstance;

final class ReleaseTask
{
    public function execute(
        TaskInstance $taskInstance,
    ): TaskInstance {

        $taskInstance->update([
            'assigned_to_id' => null,
            'status' => TaskStatus::Queue,
        ]);

        event(
            new TaskReleased(
                $taskInstance
            )
        );

        return $taskInstance->refresh();
    }
}
