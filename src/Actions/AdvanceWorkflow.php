<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkflowStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\Transition;
use Rimba\Work\Models\WorkflowInstance;
use Rimba\Work\Services\WorkflowConditionEvaluator;
use Rimba\Work\Services\WorkPackageJoinService;
use RuntimeException;

class AdvanceWorkflow
{
    public function __construct(
        private WorkflowConditionEvaluator $workflowConditionEvaluator,
        private WorkPackageJoinService $workPackageJoinService,
        private CreateTask $createTask,
    ) {}

    public function execute(
        WorkflowInstance $workflow,
        Task $completedTask,
        ?Model $actor = null,
    ): WorkflowInstance {
        return DB::transaction(function () use (
            $workflow,
            $completedTask,
            $actor,
        ): WorkflowInstance {
            $workflow = WorkflowInstance::query()
                ->lockForUpdate()
                ->findOrFail($workflow->getKey());

            if ($workflow->status !== WorkflowStatus::Active) {
                return $workflow;
            }

            $workPackage = $completedTask->workpackage_snapshot;
            $routes = $workPackage['next'] ?? [];

            foreach ($routes as $route) {
                $route = is_string($route)
                    ? ['workpackage' => $route]
                    : $route;

                $nextSlug = $route['workpackage'] ?? null;

                if (! is_string($nextSlug) || blank($nextSlug)) {
                    throw new RuntimeException(
                        'A next route is missing its WorkPackage slug.'
                    );
                }

                if (! $this->workflowConditionEvaluator->matches(
                    $workflow,
                    $route['when'] ?? null,
                )) {
                    continue;
                }

                $nextDefinition = $this->findWorkPackage(
                    $workflow->definition_snapshot,
                    $nextSlug,
                );

                if (! $this->workPackageJoinService->isSatisfied(
                    $workflow,
                    $nextDefinition,
                )) {
                    continue;
                }

                $toTask = $this->createTask->execute(
                    $workflow,
                    $nextDefinition,
                    $workflow->initiator,
                );

                Transition::query()->firstOrCreate(
                    [
                        'workflow_instance_id' => $workflow->getKey(),
                        'from_task_id' => $completedTask->getKey(),
                        'to_task_id' => $toTask->getKey(),
                        'event' => 'completed',
                    ],
                    [
                        'from_workpackage_slug' => $completedTask->workpackage_slug,

                        'to_workpackage_slug' => $nextSlug,
                        'actor_type' => $actor?->getMorphClass(),
                        'actor_id' => $actor?->getKey(),
                        'performed_at' => now(),
                    ],
                );
            }

            return $this->completeWhenFinished($workflow);
        });
    }

    private function findWorkPackage(
        array $definition,
        string $slug,
    ): array {
        $workPackage = collect(
            $definition['workpackages'] ?? []
        )->firstWhere('slug', $slug);

        if (! is_array($workPackage)) {
            throw new RuntimeException(
                "WorkPackage [{$slug}] was not found."
            );
        }

        return $workPackage;
    }

    private function completeWhenFinished(
        WorkflowInstance $workflow,
    ): WorkflowInstance {
        $hasOpenTasks = $workflow->tasks()
            ->whereIn('status', [
                TaskStatus::Pending,
                TaskStatus::Ready,
                TaskStatus::Assigned,
                TaskStatus::Started,
                TaskStatus::Waiting,
            ])
            ->exists();

        if ($hasOpenTasks) {
            return $workflow->fresh();
        }

        $workflow->update([
            'status' => WorkflowStatus::Completed,
            'current_workpackage_slug' => null,
            'completed_at' => now(),
        ]);

        return $workflow->fresh();
    }
}
