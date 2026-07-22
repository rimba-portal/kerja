<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\Task;

final class UpdateTask
{
    public function execute(
        Task $task,
        array $attributes,
    ): Task {

        $task->update($attributes);

        return $task->refresh();
    }
}
