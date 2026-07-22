<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use Rimba\Work\Models\Task;

final class TaskObserver
{
    public function creating(
        Task $task,
    ): void {

        $task->active ??= true;
    }
}
