<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use InvalidArgumentException;
use Rimba\Work\Models\WorkflowInstance;

class WorkflowConditionEvaluator
{
    public function matches(
        WorkflowInstance $workflow,
        ?array $condition,
    ): bool {
        if ($condition === null || $condition === []) {
            return true;
        }

        if (isset($condition['all'])) {
            return collect($condition['all'])
                ->every(
                    fn (array $child): bool => $this->matches($workflow, $child)
                );
        }

        if (isset($condition['any'])) {
            return collect($condition['any'])
                ->contains(
                    fn (array $child): bool => $this->matches($workflow, $child)
                );
        }

        if (isset($condition['not'])) {
            return ! $this->matches(
                $workflow,
                $condition['not'],
            );
        }

        $path = $condition['path'] ?? null;
        $operator = strtolower(
            (string) ($condition['operator'] ?? 'equals')
        );
        $expected = $condition['value'] ?? null;

        if (! is_string($path) || blank($path)) {
            throw new InvalidArgumentException(
                'A workflow condition must contain a path.'
            );
        }

        $actual = data_get(
            $workflow->context ?? [],
            $path,
        );

        return match ($operator) {
            'equals' => $actual === $expected,
            'not_equals' => $actual !== $expected,
            'greater_than' => $actual > $expected,
            'greater_than_or_equal' => $actual >= $expected,
            'less_than' => $actual < $expected,
            'less_than_or_equal' => $actual <= $expected,

            'in' => is_array($expected)
                && in_array($actual, $expected, true),

            'not_in' => is_array($expected)
                && ! in_array($actual, $expected, true),

            'contains' => $this->contains($actual, $expected),

            'exists' => data_get(
                $workflow->context ?? [],
                $path,
                '__missing__',
            ) !== '__missing__',

            'blank' => blank($actual),
            'filled' => filled($actual),
            'true' => $actual === true,
            'false' => $actual === false,

            default => throw new InvalidArgumentException(
                "Unsupported condition operator [{$operator}]."
            ),
        };
    }

    private function contains(
        mixed $actual,
        mixed $expected,
    ): bool {
        if (is_array($actual)) {
            return in_array($expected, $actual, true);
        }

        if (is_string($actual) && is_string($expected)) {
            return str_contains($actual, $expected);
        }

        return false;
    }
}
