<?php

declare(strict_types=1);

namespace Rimba\Work\Listeners;

use Rimba\Trail\Models\AuditLog;
use Rimba\Work\Actions\ActivateChecklist;
use Rimba\Work\Actions\CompleteChecklist;
use Rimba\Work\Actions\CompleteWorkPackage;
use Rimba\Work\Actions\CreateTaskInstance;
use Rimba\Work\Enums\ChecklistStatus;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Events\ChecklistActivated;
use Rimba\Work\Events\ChecklistCompleted;
use Rimba\Work\Events\TaskCancelled;
use Rimba\Work\Events\TaskCompleted;
use Rimba\Work\Events\TaskSkipped;
use Rimba\Work\Events\WorkPackageStarted;

final class WriteAuditLog
{
    public function handle(
        object $event,
    ): void {

        $model =
            $event->taskInstance
            ?? $event->checklistInstance
            ?? $event->workPackageInstance
            ?? null;

        if (! $model) {
            return;
        }

        AuditLog::create([
            'ref_type' => $model::class,
            'ref_id' => $model->getKey(),
            'actor' => 'system',
            'action' => class_basename($event),
            'result' => 'success',
            'reason' => null,
            'metadata' => [],
        ]);
    }
}

