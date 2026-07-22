<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Events\ChecklistCompleted;
use Rimba\Work\Models\ChecklistInstance;

final class CompleteChecklist
{
    public function execute(
        ChecklistInstance $checklistInstance,
    ): ChecklistInstance {

        $checklistInstance->update([
            'status' => ChecklistStatus::Completed,
            'completed_at' => now(),
        ]);

        event(
            new ChecklistCompleted(
                $checklistInstance
            )
        );

        return $checklistInstance->refresh();
    }
}
