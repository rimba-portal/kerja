<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Artifact;
use Rimba\Work\Models\Task;

class CompleteTask
{
    public function execute(
        Task $task,
        array $result = [],
        ?Model $actor = null
    ): Task {
        return DB::transaction(function () use (
            $task,
            $result,
            $actor
        ): Task {
            $task->update([
                'status' => TaskStatus::Completed,
                'result' => $result,
                'completed_at' => now(),
            ]);

            foreach ($result['artifacts'] ?? [] as $artifact) {
                Artifact::query()->create([
                    'workflow_instance_id' => $task->workflow_instance_id,
                    'produced_by_task_id' => $task->getKey(),
                    'key' => $artifact['key'],
                    'name' => $artifact['name']
                        ?? str($artifact['key'])->headline(),
                    'type' => $artifact['type'] ?? 'data',
                    'value' => $artifact['value'] ?? null,
                    'metadata' => $artifact['metadata'] ?? null,
                    'produced_at' => now(),
                ]);
            }

            app(AdvanceWorkflow::class)->execute(
                $task->workflowInstance,
                $task,
                $actor
            );

            return $task->fresh();
        });
    }
}
