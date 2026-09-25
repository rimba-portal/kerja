<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\ExecutionType;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Enums\WorkflowStatus;
use Rimba\Work\Models\Artifact;
use Rimba\Work\Models\Task;
use Rimba\Work\Models\Transition;
use Rimba\Work\Models\WorkflowInstance;
use Rimba\Work\Services\ActorResolverService;
use Rimba\Work\Services\HandlerRegistry;
use Rimba\Work\Services\WorkflowDefinitionRepository;
use RuntimeException;
class StartWorkflow
{
    public function __construct(
        private WorkflowDefinitionRepository $definitions,
        private CreateTask $createTask,
    ) {}

    public function execute(
        string $workflowSlug,
        ?Model $subject = null,
        ?Model $initiator = null,
        array $context = []
    ): WorkflowInstance {
        $definition = $this->definitions->find($workflowSlug);

        return DB::transaction(function () use (
            $definition,
            $subject,
            $initiator,
            $context
        ): WorkflowInstance {
            $instance = WorkflowInstance::query()->create([
                'workflow_slug' => $definition['slug'],
                'workflow_version' => $definition['version'],
                'definition_snapshot' => $definition,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'initiator_type' => $initiator?->getMorphClass(),
                'initiator_id' => $initiator?->getKey(),
                'current_workpackage_slug' =>
                    $definition['first_workpackage'],
                'status' => WorkflowStatus::Active,
                'context' => $context,
                'started_at' => now(),
            ]);

            $this->createTask->execute(
                $instance,
                $this->workPackage(
                    $definition,
                    $definition['first_workpackage']
                ),
                $initiator
            );

            return $instance->fresh();
        });
    }

    private function workPackage(
        array $definition,
        string $slug
    ): array {
        foreach ($definition['workpackages'] as $workPackage) {
            if ($workPackage['slug'] === $slug) {
                return $workPackage;
            }
        }

        throw new RuntimeException(
            "WorkPackage [{$slug}] was not found."
        );
    }
}

