<?php

declare(strict_types=1);

namespace Rimba\Work\Listeners;

use Rimba\Work\Actions\CreateTaskInstance;
use Rimba\Work\Events\ChecklistActivated;

final class CreateTaskInstances
{
    public function handle(
        ChecklistActivated $event,
    ): void {

        $checklistInstance =
            $event->checklistInstance;

        foreach (
            $checklistInstance
                ->checklist
                ->tasks()
                ->where('active', true)
                ->get() as $task
        ) {

            app(CreateTaskInstance::class)
                ->execute(
                    $checklistInstance,
                    $task,
                );
        }
    }
}
