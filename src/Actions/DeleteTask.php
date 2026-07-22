<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\Task;

final class DeleteTask
{
    public function execute(
        Task $task,
    ): void {

        $task->delete();
    }
}
