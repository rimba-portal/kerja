<?php

declare(strict_types=1);

namespace Rimba\Work\Listeners;

use Rimba\Trail\Models\AuditLog;

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
