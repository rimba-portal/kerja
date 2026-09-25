<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use InvalidArgumentException;
use Rimba\Work\Enums\ActivityType;
use Rimba\Work\Enums\ExecutionType;

class WorkflowDefinitionValidator
{
    public function validate(array $definition): array
    {
        $errors = [];

        foreach (
            [
                'slug',
                'title',
                'version',
                'first_workpackage',
                'workpackages',
            ] as $field
        ) {
            if (! array_key_exists($field, $definition)) {
                $errors[] =
                    "Missing required field [{$field}].";
            }
        }

        if ($errors !== []) {
            return $errors;
        }

        if (! is_array($definition['workpackages'])) {
            return [
                'workpackages must be an array.',
            ];
        }

        $slugs = [];

        foreach (
            $definition['workpackages'] as $index => $workPackage
        ) {
            foreach (
                [
                    'slug',
                    'actor',
                    'activity_type',
                    'business_object',
                    'execution_type',
                    'suppliers',
                    'inputs',
                    'outputs',
                    'customers',
                ] as $field
            ) {
                if (! array_key_exists(
                    $field,
                    $workPackage
                )) {
                    $errors[] =
                        "WorkPackage {$index} is missing {$field}.";
                }
            }

            if (! isset($workPackage['slug'])) {
                continue;
            }

            $slugs[] = $workPackage['slug'];

            if (
                isset($workPackage['activity_type'])
                && ActivityType::tryFrom(
                    strtolower(
                        (string) $workPackage['activity_type']
                    )
                ) === null
            ) {
                $errors[] =
                    "WorkPackage [{$workPackage['slug']}] has invalid activity_type.";
            }

            if (
                isset($workPackage['execution_type'])
                && ExecutionType::tryFrom(
                    strtolower(
                        (string) $workPackage['execution_type']
                    )
                ) === null
            ) {
                $errors[] =
                    "WorkPackage [{$workPackage['slug']}] has invalid execution_type.";
            }
        }

        if (
            count($slugs)
            !== count(array_unique($slugs))
        ) {
            $errors[] =
                'WorkPackage slugs must be unique.';
        }

        if (
            ! in_array(
                $definition['first_workpackage'],
                $slugs,
                true
            )
        ) {
            $errors[] =
                'first_workpackage must reference an existing WorkPackage.';
        }

        foreach (
            $definition['workpackages'] as $workPackage
        ) {
            foreach (
                $workPackage['next'] ?? [] as $next
            ) {
                $target = is_array($next)
                    ? ($next['workpackage'] ?? null)
                    : $next;

                if (
                    $target
                    && ! in_array(
                        $target,
                        $slugs,
                        true
                    )
                ) {
                    $errors[] =
                        "Unknown next WorkPackage [{$target}] from [{$workPackage['slug']}].";
                }
            }
        }

        foreach (
            $definition['workpackages'] as $workPackage
        ) {
            array_push(
                $errors,
                ...$this->validateWorkPackage(
                    $workPackage,
                    $slugs,
                )
            );
        }

        array_push(
            $errors,
            ...$this->detectCycles(
                $definition
            )
        );

        array_push(
            $errors,
            ...$this->detectUnreachableWorkPackages(
                $definition
            )
        );

        return array_values(
            array_unique($errors)
        );
    }

    private function validateWorkPackage(
        array $workPackage,
        array $slugs,
    ): array {
        $errors = [];

        $slug =
            $workPackage['slug']
            ?? '(unknown)';

        $executionType =
            ExecutionType::tryFrom(
                strtolower(
                    (string) (
                        $workPackage['execution_type']
                        ?? ''
                    )
                )
            );

        if (
            $executionType === ExecutionType::Rimba
            && blank(
                $workPackage['handler']
                ?? null
            )
        ) {
            $errors[] =
                "Rimba WorkPackage [{$slug}] must define a handler.";
        }

        if (
            $executionType === ExecutionType::EventDriven
            && blank(
                $workPackage['trigger_event']
                ?? null
            )
        ) {
            $errors[] =
                "Event-driven WorkPackage [{$slug}] must define trigger_event.";
        }

        $join = strtolower(
            (string) (
                $workPackage['join']
                ?? 'all'
            )
        );

        if (
            ! in_array(
                $join,
                ['all', 'any'],
                true
            )
        ) {
            $errors[] =
                "WorkPackage [{$slug}] has invalid join [{$join}].";
        }

        if (
            isset($workPackage['wait_for'])
            && ! is_array(
                $workPackage['wait_for']
            )
        ) {
            $errors[] =
                "WorkPackage [{$slug}] wait_for must be an array.";
        }

        foreach (
            $workPackage['wait_for']
            ?? [] as $dependency
        ) {
            if (
                ! in_array(
                    $dependency,
                    $slugs,
                    true
                )
            ) {
                $errors[] =
                    "WorkPackage [{$slug}] waits for unknown WorkPackage [{$dependency}].";
            }

            if (
                $dependency === $slug
            ) {
                $errors[] =
                    "WorkPackage [{$slug}] cannot wait for itself.";
            }
        }

        foreach (
            $workPackage['next']
            ?? [] as $route
        ) {
            if (
                is_array($route)
                && blank(
                    $route['workpackage']
                    ?? null
                )
            ) {
                $errors[] =
                    "WorkPackage [{$slug}] contains a route without a target.";
            }

            if (is_array($route)) {
                array_push(
                    $errors,
                    ...$this->validateCondition(
                        $route['when']
                        ?? null,
                        $slug
                    )
                );
            }
        }

        return $errors;
    }

    private function validateCondition(
        mixed $condition,
        string $slug,
    ): array {
        if ($condition === null) {
            return [];
        }

        $errors = [];

        $operators = [
            'equals',
            'not_equals',
            'greater_than',
            'greater_than_or_equal',
            'less_than',
            'less_than_or_equal',
            'in',
            'not_in',
            'contains',
            'exists',
            'blank',
            'filled',
            'true',
            'false',
        ];

        if (
            isset($condition['operator'])
            && ! in_array(
                $condition['operator'],
                $operators,
                true
            )
        ) {
            $errors[] =
                "WorkPackage [{$slug}] contains invalid condition operator [{$condition['operator']}].";
        }

        return $errors;
    }

    private function detectCycles(
        array $definition
    ): array {
        $graph = [];

        foreach (
            $definition['workpackages'] as $workPackage
        ) {
            $graph[
                $workPackage['slug']
            ] = [];

            foreach (
                $workPackage['next']
                ?? [] as $next
            ) {
                $graph[
                    $workPackage['slug']
                ][] = is_array(
                    $next
                )
                    ? (
                        $next['workpackage']
                        ?? null
                    )
                    : $next;
            }
        }

        $visited = [];
        $stack = [];

        foreach (
            array_keys($graph) as $node
        ) {
            if (
                $this->hasCycle(
                    $node,
                    $graph,
                    $visited,
                    $stack
                )
            ) {
                return [
                    "Workflow contains circular routing involving [{$node}].",
                ];
            }
        }

        return [];
    }

    private function hasCycle(
        string $node,
        array $graph,
        array &$visited,
        array &$stack,
    ): bool {
        if (
            ($stack[$node] ?? false)
        ) {
            return true;
        }

        if (
            ($visited[$node] ?? false)
        ) {
            return false;
        }

        $visited[$node] = true;
        $stack[$node] = true;

        foreach (
            $graph[$node]
            ?? [] as $next
        ) {
            if (
                $next !== null
                && $this->hasCycle(
                    $next,
                    $graph,
                    $visited,
                    $stack
                )
            ) {
                return true;
            }
        }

        $stack[$node] = false;

        return false;
    }

    private function detectUnreachableWorkPackages(
        array $definition
    ): array {
        $graph = [];

        foreach (
            $definition['workpackages'] as $workPackage
        ) {
            $graph[
                $workPackage['slug']
            ] = [];

            foreach (
                $workPackage['next']
                ?? [] as $next
            ) {
                $graph[
                    $workPackage['slug']
                ][] = is_array(
                    $next
                )
                    ? (
                        $next['workpackage']
                        ?? null
                    )
                    : $next;
            }
        }

        $reachable = [];

        $this->walk(
            $definition['first_workpackage'],
            $graph,
            $reachable
        );

        $errors = [];

        foreach (
            array_keys($graph) as $slug
        ) {
            if (
                ! isset(
                    $reachable[$slug]
                )
            ) {
                $errors[] =
                    "WorkPackage [{$slug}] is unreachable.";
            }
        }

        return $errors;
    }

    private function walk(
        string $node,
        array $graph,
        array &$reachable,
    ): void {
        if (
            isset(
                $reachable[$node]
            )
        ) {
            return;
        }

        $reachable[$node] = true;

        foreach (
            $graph[$node]
            ?? [] as $next
        ) {
            if ($next !== null) {
                $this->walk(
                    $next,
                    $graph,
                    $reachable
                );
            }
        }
    }

    public function assert(
        array $definition
    ): void {
        $errors =
            $this->validate(
                $definition
            );

        if ($errors !== []) {
            throw new InvalidArgumentException(
                implode(
                    PHP_EOL,
                    $errors
                )
            );
        }
    }
}
