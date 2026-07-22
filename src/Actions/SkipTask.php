<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Events\TaskSkipped;
use Rimba\Work\Models\TaskInstance;

final class SkipTask
{
    public function execute(
        TaskInstance $taskInstance,
    ): TaskInstance {

        $taskInstance->update([
            'status' => TaskStatus::Skipped,
        ]);

        event(
            new TaskSkipped(
                $taskInstance
            )
        );

        return $taskInstance->refresh();
    }
}
