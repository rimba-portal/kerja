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
            ['slug', 'title', 'version', 'first_workpackage', 'workpackages'] as $field
        ) {
            if (! array_key_exists($field, $definition)) {
                $errors[] = "Missing required field: {$field}.";
            }
        }

        if ($errors !== []) {
            return $errors;
        }

        if (! is_array($definition['workpackages'])) {
            return ['workpackages must be an array.'];
        }

        $slugs = [];

        foreach ($definition['workpackages'] as $index => $workPackage) {
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
                    strtolower($workPackage['activity_type'])
                ) === null
            ) {
                $errors[] =
                    "WorkPackage {$workPackage['slug']} has invalid "
                    .'activity_type.';
            }

            if (
                isset($workPackage['execution_type'])
                && ExecutionType::tryFrom(
                    strtolower($workPackage['execution_type'])
                ) === null
            ) {
                $errors[] =
                    "WorkPackage {$workPackage['slug']} has invalid "
                    .'execution_type.';
            }
        }

        if (count($slugs) !== count(array_unique($slugs))) {
            $errors[] = 'WorkPackage slugs must be unique.';
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

        foreach ($definition['workpackages'] as $workPackage) {
            foreach ($workPackage['next'] ?? [] as $next) {
                $target = is_array($next)
                    ? ($next['workpackage'] ?? null)
                    : $next;

                if ($target && ! in_array($target, $slugs, true)) {
                    $errors[] =
                        "Unknown next WorkPackage [{$target}] from "
                        ."[{$workPackage['slug']}].";
                }
            }
        }

        return $errors;
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
