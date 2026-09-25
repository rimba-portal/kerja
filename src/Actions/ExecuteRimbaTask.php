<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Enums\ExecutionType;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Services\HandlerRegistry;
use RuntimeException;
use Throwable;

class ExecuteRimbaTask
{
    public function __construct(
        private HandlerRegistry $handlerRegistry,
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
            'failed_at' => null,
            'failure_reason' => null,
        ]);

        try {
            $handler = $this->handlerRegistry->resolve(
                $task->handler
            );

            if (! method_exists($handler, 'execute')) {
                throw new RuntimeException(
                    "Handler [{$task->handler}] must provide execute()."
                );
            }

            $result = $handler->execute($task);

            return app(CompleteTask::class)->execute(
                $task,
                is_array($result)
                    ? $result
                    : ['value' => $result],
            );
        } catch (Throwable $throwable) {
            $task->update([
                'status' => TaskStatus::Failed,
                'failed_at' => now(),
                'failure_reason' => $throwable->getMessage(),
            ]);

            $failurePolicy = data_get(
                $task->workpackage_snapshot,
                'failure.workflow',
                'fail',
            );

            if ($failurePolicy === 'fail') {
                app(FailWorkflow::class)->execute(
                    $task->workflowInstance,
                    $throwable->getMessage(),
                );
            }

            throw $throwable;
        }
    }
}
