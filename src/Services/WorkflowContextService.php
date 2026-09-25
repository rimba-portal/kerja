<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Rimba\Work\Models\WorkflowInstance;

class WorkflowContextService
{
    public function all(WorkflowInstance $workflow): array
    {
        return $workflow->context ?? [];
    }

    public function get(
        WorkflowInstance $workflow,
        string $key,
        mixed $default = null,
    ): mixed {
        return data_get(
            $workflow->context ?? [],
            $key,
            $default,
        );
    }

    public function set(
        WorkflowInstance $workflow,
        string $key,
        mixed $value,
    ): WorkflowInstance {
        $context = $workflow->context ?? [];

        data_set($context, $key, $value);

        $workflow->update([
            'context' => $context,
        ]);

        return $workflow->fresh();
    }

    public function merge(
        WorkflowInstance $workflow,
        array $values,
    ): WorkflowInstance {
        $workflow->update([
            'context' => array_replace_recursive(
                $workflow->context ?? [],
                $values,
            ),
        ]);

        return $workflow->fresh();
    }

    public function forget(
        WorkflowInstance $workflow,
        string $key,
    ): WorkflowInstance {
        $context = $workflow->context ?? [];

        data_forget($context, $key);

        $workflow->update([
            'context' => $context,
        ]);

        return $workflow->fresh();
    }
}
