<?php

declare(strict_types=1);

namespace Rimba\Work\Listeners;

use Rimba\Work\Actions\CompleteChecklist;
use Rimba\Work\Enums\TaskStatus;

final class AutoCompleteChecklist
{
    public function handle(
        object $event,
    ): void {

        $checklistInstance =
            $event
                ->taskInstance
                ->checklistInstance;

        $remainingTasks =
            $checklistInstance
                ->taskInstances()
                ->whereNotIn('status', [
                    TaskStatus::Completed,
                    TaskStatus::Skipped,
                    TaskStatus::Cancelled,
                ])
                ->count();

        if ($remainingTasks > 0) {
            return;
        }

        app(CompleteChecklist::class)
            ->execute(
                $checklistInstance,
            );
    }
}
