<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use InvalidArgumentException;
use Rimba\Work\Enums\ActivityType;
use Rimba\Work\Enums\ExecutionType;

class WorkflowDefinitionValidator
{
    private const CONDITION_OPERATORS = [
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
                $errors[] = "Missing required field [{$field}].";
            }
        }

        if ($errors !== []) {
            return $errors;
        }

        if (
            ! is_string($definition['slug'])
            || blank($definition['slug'])
        ) {
            $errors[] = 'Workflow slug must be a non-empty string.';
        }

        if (
            ! is_string($definition['title'])
            || blank($definition['title'])
        ) {
            $errors[] = 'Workflow title must be a non-empty string.';
        }

        if (
            ! is_int($definition['version'])
            || $definition['version'] < 1
        ) {
            $errors[] = 'Workflow version must be a positive integer.';
        }

        if (
            ! is_string($definition['first_workpackage'])
            || blank($definition['first_workpackage'])
        ) {
            $errors[] =
                'first_workpackage must be a non-empty string.';
        }

        if (! is_array($definition['workpackages'])) {
            $errors[] = 'workpackages must be an array.';

            return array_values(array_unique($errors));
        }

        if ($definition['workpackages'] === []) {
            $errors[] =
                'Workflow must define at least one WorkPackage.';

            return array_values(array_unique($errors));
        }

        $slugs = [];

        foreach (
            $definition['workpackages']
            as $index => $workPackage
        ) {
            if (! is_array($workPackage)) {
                $errors[] =
                    "WorkPackage at index [{$index}] must be an object.";

                continue;
            }

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
                if (! array_key_exists($field, $workPackage)) {
                    $errors[] =
                        "WorkPackage at index [{$index}] is missing [{$field}].";
                }
            }

            $slug = $workPackage['slug'] ?? null;

            if (! is_string($slug) || blank($slug)) {
                $errors[] =
                    "WorkPackage at index [{$index}] must have a non-empty slug.";

                continue;
            }

            $slugs[] = $slug;

            if (
                isset($workPackage['activity_type'])
                && (
                    ! is_string($workPackage['activity_type'])
                    || ActivityType::tryFrom(
                        strtolower($workPackage['activity_type'])
                    ) === null
                )
            ) {
                $errors[] =
                    "WorkPackage [{$slug}] has invalid activity_type.";
            }

            if (
                isset($workPackage['execution_type'])
                && (
                    ! is_string($workPackage['execution_type'])
                    || ExecutionType::tryFrom(
                        strtolower($workPackage['execution_type'])
                    ) === null
                )
            ) {
                $errors[] =
                    "WorkPackage [{$slug}] has invalid execution_type.";
            }

            foreach (
                [
                    'suppliers',
                    'inputs',
                    'outputs',
                    'customers',
                ] as $field
            ) {
                if (
                    array_key_exists($field, $workPackage)
                    && ! is_array($workPackage[$field])
                ) {
                    $errors[] =
                        "WorkPackage [{$slug}] field [{$field}] must be an array.";
                }
            }

            if (
                isset($workPackage['next'])
                && ! is_array($workPackage['next'])
            ) {
                $errors[] =
                    "WorkPackage [{$slug}] next must be an array.";
            }
        }

        if (count($slugs) !== count(array_unique($slugs))) {
            $errors[] = 'WorkPackage slugs must be unique.';
        }

        if (
            ! in_array(
                $definition['first_workpackage'],
                $slugs,
                true,
            )
        ) {
            $errors[] =
                'first_workpackage must reference an existing WorkPackage.';
        }

        foreach ($definition['workpackages'] as $workPackage) {
            if (! is_array($workPackage)) {
                continue;
            }

            array_push(
                $errors,
                ...$this->validateWorkPackage(
                    $workPackage,
                    $slugs,
                ),
            );
        }

        if ($this->canBuildGraph($definition)) {
            array_push(
                $errors,
                ...$this->detectCycles($definition),
            );

            array_push(
                $errors,
                ...$this->detectUnreachableWorkPackages(
                    $definition,
                ),
            );
        }

        return array_values(array_unique($errors));
    }

    private function validateWorkPackage(
        array $workPackage,
        array $slugs,
    ): array {
        $errors = [];
        $slug = $workPackage['slug'] ?? '(unknown)';

        $executionType = null;

        if (is_string($workPackage['execution_type'] ?? null)) {
            $executionType = ExecutionType::tryFrom(
                strtolower($workPackage['execution_type'])
            );
        }

        if (
            $executionType === ExecutionType::Rimba
            && blank($workPackage['handler'] ?? null)
        ) {
            $errors[] =
                "Rimba WorkPackage [{$slug}] must define a handler.";
        }

        if (
            $executionType === ExecutionType::EventDriven
            && blank($workPackage['trigger_event'] ?? null)
        ) {
            $errors[] =
                "Event-driven WorkPackage [{$slug}] must define trigger_event.";
        }

        if (
            isset($workPackage['handler'])
            && ! is_string($workPackage['handler'])
        ) {
            $errors[] =
                "WorkPackage [{$slug}] handler must be a string.";
        }

        if (
            isset($workPackage['trigger_event'])
            && ! is_string($workPackage['trigger_event'])
        ) {
            $errors[] =
                "WorkPackage [{$slug}] trigger_event must be a string.";
        }

        if (
            isset($workPackage['wait_for'])
            && ! is_array($workPackage['wait_for'])
        ) {
            $errors[] =
                "WorkPackage [{$slug}] wait_for must be an array.";
        }

        $dependencies = is_array(
            $workPackage['wait_for'] ?? null
        )
            ? $workPackage['wait_for']
            : [];

        if (
            $dependencies !== []
            && ! array_key_exists('join', $workPackage)
        ) {
            $errors[] =
                "WorkPackage [{$slug}] must define join when wait_for is used.";
        }

        if (
            array_key_exists('join', $workPackage)
            && $dependencies === []
        ) {
            $errors[] =
                "WorkPackage [{$slug}] defines join without wait_for.";
        }

        if (array_key_exists('join', $workPackage)) {
            $join = strtolower(
                (string) $workPackage['join']
            );

            if (! in_array($join, ['all', 'any'], true)) {
                $errors[] =
                    "WorkPackage [{$slug}] has invalid join [{$join}].";
            }
        }

        if (
            count($dependencies)
            !== count(array_unique($dependencies))
        ) {
            $errors[] =
                "WorkPackage [{$slug}] wait_for dependencies must be unique.";
        }

        foreach ($dependencies as $dependency) {
            if (! is_string($dependency) || blank($dependency)) {
                $errors[] =
                    "WorkPackage [{$slug}] contains an invalid wait_for dependency.";

                continue;
            }

            if (! in_array($dependency, $slugs, true)) {
                $errors[] =
                    "WorkPackage [{$slug}] waits for unknown WorkPackage [{$dependency}].";
            }

            if ($dependency === $slug) {
                $errors[] =
                    "WorkPackage [{$slug}] cannot wait for itself.";
            }
        }

        $routes = $workPackage['next'] ?? [];

        if (! is_array($routes)) {
            return $errors;
        }

        foreach ($routes as $routeIndex => $route) {
            if (is_string($route)) {
                if (blank($route)) {
                    $errors[] =
                        "WorkPackage [{$slug}] contains an empty route target.";

                    continue;
                }

                if (! in_array($route, $slugs, true)) {
                    $errors[] =
                        "Unknown next WorkPackage [{$route}] from [{$slug}].";
                }

                continue;
            }

            if (! is_array($route)) {
                $errors[] =
                    "WorkPackage [{$slug}] route [{$routeIndex}] must be a string or object.";

                continue;
            }

            $target = $route['workpackage'] ?? null;

            if (! is_string($target) || blank($target)) {
                $errors[] =
                    "WorkPackage [{$slug}] contains a route without a valid target.";
            } elseif (! in_array($target, $slugs, true)) {
                $errors[] =
                    "Unknown next WorkPackage [{$target}] from [{$slug}].";
            }

            if (array_key_exists('when', $route)) {
                array_push(
                    $errors,
                    ...$this->validateCondition(
                        $route['when'],
                        $slug,
                        "next.{$routeIndex}.when",
                    ),
                );
            }
        }

        return $errors;
    }

    private function validateCondition(
        mixed $condition,
        string $slug,
        string $location,
    ): array {
        if (! is_array($condition) || $condition === []) {
            return [
                "WorkPackage [{$slug}] condition [{$location}] must be a non-empty object.",
            ];
        }

        $errors = [];

        $logicalKeys = array_values(
            array_intersect(
                ['all', 'any', 'not'],
                array_keys($condition),
            )
        );

        if (count($logicalKeys) > 1) {
            $errors[] =
                "WorkPackage [{$slug}] condition [{$location}] may define only one of all, any, or not.";

            return $errors;
        }

        if ($logicalKeys !== []) {
            $logicalKey = $logicalKeys[0];

            $unexpectedKeys = array_diff(
                array_keys($condition),
                [$logicalKey],
            );

            if ($unexpectedKeys !== []) {
                $errors[] =
                    "WorkPackage [{$slug}] condition [{$location}] cannot combine [{$logicalKey}] with simple condition fields.";
            }

            if (in_array($logicalKey, ['all', 'any'], true)) {
                $children = $condition[$logicalKey];

                if (! is_array($children) || $children === []) {
                    $errors[] =
                        "WorkPackage [{$slug}] condition [{$location}.{$logicalKey}] must contain at least one condition.";

                    return $errors;
                }

                foreach ($children as $index => $child) {
                    array_push(
                        $errors,
                        ...$this->validateCondition(
                            $child,
                            $slug,
                            "{$location}.{$logicalKey}.{$index}",
                        ),
                    );
                }

                return $errors;
            }

            array_push(
                $errors,
                ...$this->validateCondition(
                    $condition['not'],
                    $slug,
                    "{$location}.not",
                ),
            );

            return $errors;
        }

        $path = $condition['path'] ?? null;
        $operator = strtolower(
            (string) ($condition['operator'] ?? 'equals')
        );

        if (! is_string($path) || blank($path)) {
            $errors[] =
                "WorkPackage [{$slug}] condition [{$location}] must define a non-empty path.";
        }

        if (
            ! in_array(
                $operator,
                self::CONDITION_OPERATORS,
                true,
            )
        ) {
            $errors[] =
                "WorkPackage [{$slug}] condition [{$location}] contains invalid operator [{$operator}].";

            return $errors;
        }

        $operatorsWithoutValue = [
            'exists',
            'blank',
            'filled',
            'true',
            'false',
        ];

        if (
            ! in_array(
                $operator,
                $operatorsWithoutValue,
                true,
            )
            && ! array_key_exists('value', $condition)
        ) {
            $errors[] =
                "WorkPackage [{$slug}] condition [{$location}] using [{$operator}] must define value.";
        }

        if (
            in_array($operator, ['in', 'not_in'], true)
            && (
                ! array_key_exists('value', $condition)
                || ! is_array($condition['value'])
            )
        ) {
            $errors[] =
                "WorkPackage [{$slug}] condition [{$location}] using [{$operator}] requires an array value.";
        }

        return $errors;
    }

    private function canBuildGraph(array $definition): bool
    {
        if (
            ! isset($definition['workpackages'])
            || ! is_array($definition['workpackages'])
            || ! is_string(
                $definition['first_workpackage'] ?? null
            )
        ) {
            return false;
        }

        foreach ($definition['workpackages'] as $workPackage) {
            if (
                ! is_array($workPackage)
                || ! is_string($workPackage['slug'] ?? null)
                || blank($workPackage['slug'])
                || (
                    isset($workPackage['next'])
                    && ! is_array($workPackage['next'])
                )
            ) {
                return false;
            }
        }

        return true;
    }

    private function buildGraph(array $definition): array
    {
        $graph = [];

        foreach ($definition['workpackages'] as $workPackage) {
            $slug = $workPackage['slug'];
            $graph[$slug] = [];

            foreach ($workPackage['next'] ?? [] as $next) {
                $target = is_array($next)
                    ? ($next['workpackage'] ?? null)
                    : $next;

                if (is_string($target) && filled($target)) {
                    $graph[$slug][] = $target;
                }
            }
        }

        return $graph;
    }

    private function detectCycles(array $definition): array
    {
        $graph = $this->buildGraph($definition);
        $visited = [];
        $activePath = [];

        foreach (array_keys($graph) as $node) {
            if (
                $this->hasCycle(
                    $node,
                    $graph,
                    $visited,
                    $activePath,
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
        array &$activePath,
    ): bool {
        if ($activePath[$node] ?? false) {
            return true;
        }

        if ($visited[$node] ?? false) {
            return false;
        }

        $visited[$node] = true;
        $activePath[$node] = true;

        foreach ($graph[$node] ?? [] as $next) {
            if (
                array_key_exists($next, $graph)
                && $this->hasCycle(
                    $next,
                    $graph,
                    $visited,
                    $activePath,
                )
            ) {
                return true;
            }
        }

        $activePath[$node] = false;

        return false;
    }

    private function detectUnreachableWorkPackages(
        array $definition,
    ): array {
        $graph = $this->buildGraph($definition);
        $first = $definition['first_workpackage'];

        if (! array_key_exists($first, $graph)) {
            return [];
        }

        $reachable = [];

        $this->walk(
            $first,
            $graph,
            $reachable,
        );

        $errors = [];

        foreach (array_keys($graph) as $slug) {
            if (! isset($reachable[$slug])) {
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
        if (isset($reachable[$node])) {
            return;
        }

        $reachable[$node] = true;

        foreach ($graph[$node] ?? [] as $next) {
            if (array_key_exists($next, $graph)) {
                $this->walk(
                    $next,
                    $graph,
                    $reachable,
                );
            }
        }
    }

    public function assert(array $definition): void
    {
        $errors = $this->validate($definition);

        if ($errors !== []) {
            throw new InvalidArgumentException(
                implode(PHP_EOL, $errors)
            );
        }
    }
}