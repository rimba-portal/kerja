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
class ExecuteRimbaTask
{
    public function __construct(
        private HandlerRegistry $handlers,
    ) {}

    public function execute(Task $task): Task
    {
        if ($task->execution_type !== ExecutionType::Rimba) {
            throw new RuntimeException(
                "Task [{$task->uuid}] is not a Rimba Task."
            );
        }

        if (blank($task->handler)) {
            throw new RuntimeException(
                "Task [{$task->uuid}] has no handler."
            );
        }

        $task->update([
            'status' => TaskStatus::Started,
            'started_at' => now(),
        ]);

        try {
            $handler = $this->handlers->resolve($task->handler);

            if (! method_exists($handler, 'execute')) {
                throw new RuntimeException(
                    "Handler [{$task->handler}] must provide execute()."
                );
            }

            $result = $handler->execute($task);

            return app(CompleteTask::class)->execute(
                $task,
                is_array($result) ? $result : ['value' => $result]
            );
        } catch (\Throwable $exception) {
            $task->update([
                'status' => TaskStatus::Failed,
                'failed_at' => now(),
                'failure_reason' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}

