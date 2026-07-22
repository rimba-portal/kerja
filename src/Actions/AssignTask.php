<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use App\Trees\Organization\Models\Staff;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\TaskInstance;

final class AssignTask
{
    public function execute(
        TaskInstance $taskInstance,
        Staff $staff,
    ): TaskInstance {

        $taskInstance->update([
            'assigned_to_id' => $staff->id,
            'status' => TaskStatus::Active,
        ]);

        return $taskInstance->refresh();
    }
}
