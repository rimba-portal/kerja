<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use App\Trees\Organization\Models\Staff;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Events\TaskCompleted;
use Rimba\Work\Models\TaskInstance;

final class CompleteTask
{
    public function execute(
        TaskInstance $taskInstance,
        Staff $staff,
    ): TaskInstance {

        $taskInstance->update([
            'status' => TaskStatus::Completed,
            'is_completed' => true,
            'completed_by_id' => $staff->id,
            'completed_at' => now(),
        ]);

        event(
            new TaskCompleted(
                $taskInstance
            )
        );

        return $taskInstance->refresh();
    }
}
