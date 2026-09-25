<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\WorkflowStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\Transition;
use Rimba\Work\Models\WorkflowInstance;
use RuntimeException;

class AdvanceWorkflow
{
    public function execute(
        WorkflowInstance $workflow,
        Task $completedTask,
        ?Model $actor = null
    ): WorkflowInstance {
        $definition = $workflow->definition_snapshot;
        $workPackage = $completedTask->workpackage_snapshot;
        $nextDefinitions = $workPackage['next'] ?? [];

        if ($nextDefinitions === []) {
            $workflow->update([
                'status' => WorkflowStatus::Completed,
                'current_workpackage_slug' => null,
                'completed_at' => now(),
            ]);

            return $workflow->fresh();
        }

        foreach ($nextDefinitions as $nextDefinition) {
            $nextSlug = is_array($nextDefinition)
                ? $nextDefinition['workpackage']
                : $nextDefinition;

            $next = collect($definition['workpackages'])
                ->firstWhere('slug', $nextSlug);

            if (! $next) {
                throw new RuntimeException(
                    "Next WorkPackage [{$nextSlug}] was not found."
                );
            }

            $toTask = app(CreateTask::class)->execute(
                $workflow,
                $next,
                $workflow->initiator
            );

            Transition::query()->create([
                'workflow_instance_id' => $workflow->getKey(),
                'from_task_id' => $completedTask->getKey(),
                'to_task_id' => $toTask->getKey(),
                'from_workpackage_slug' => $completedTask->workpackage_slug,
                'to_workpackage_slug' => $nextSlug,
                'event' => 'completed',
                'actor_type' => $actor?->getMorphClass(),
                'actor_id' => $actor?->getKey(),
                'performed_at' => now(),
            ]);

            $workflow->update([
                'current_workpackage_slug' => $nextSlug,
            ]);
        }

        return $workflow->fresh();
    }
}
