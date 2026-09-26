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

            if (
                ! in_array(
                    $workflow->status,
                    [
                        WorkflowStatus::Active,
                        WorkflowStatus::Waiting,
                    ],
                    true,
                )
            ) {
                return $workflow;
            }

            $workPackage = $completedTask->workpackage_snapshot;
            $routes = $workPackage['next'] ?? [];
            $eligibleRouteCount = 0;

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

                if (
                    ! $this->workflowConditionEvaluator->matches(
                        $workflow,
                        $route['when'] ?? null,
                    )
                ) {
                    continue;
                }

                $eligibleRouteCount++;

                $nextDefinition = $this->findWorkPackage(
                    $workflow->definition_snapshot,
                    $nextSlug,
                );

                if (
                    ! $this->workPackageJoinService->isSatisfied(
                        $workflow,
                        $nextDefinition,
                    )
                ) {
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

            if (
                $routes !== []
                && $eligibleRouteCount === 0
            ) {
                throw new RuntimeException(
                    "No eligible next route was found after WorkPackage [{$completedTask->workpackage_slug}]."
                );
            }

            return $this->synchronizeWorkflowStatus(
                $workflow
            );
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

    private function synchronizeWorkflowStatus(
        WorkflowInstance $workflow,
    ): WorkflowInstance {
        $hasActiveTasks = $workflow->tasks()
            ->whereIn('status', [
                TaskStatus::Pending,
                TaskStatus::Ready,
                TaskStatus::Assigned,
                TaskStatus::Started,
            ])
            ->exists();

        if ($hasActiveTasks) {
            if (
                $workflow->status
                !== WorkflowStatus::Active
            ) {
                $workflow->update([
                    'status' => WorkflowStatus::Active,
                ]);
            }

            return $workflow->fresh();
        }

        $hasWaitingTasks = $workflow->tasks()
            ->where('status', TaskStatus::Waiting)
            ->exists();

        if ($hasWaitingTasks) {
            $workflow->update([
                'status' => WorkflowStatus::Waiting,
            ]);

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
