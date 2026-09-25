<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\WorkflowInstance;

class WorkPackageJoinService
{
    public function isSatisfied(
        WorkflowInstance $workflow,
        array $workPackage,
    ): bool {
        $dependencies = $workPackage['wait_for'] ?? [];

        if ($dependencies === []) {
            return true;
        }

        $join = strtolower(
            (string) ($workPackage['join'] ?? 'all')
        );

        $completed = $workflow->tasks()
            ->whereIn('workpackage_slug', $dependencies)
            ->where('status', TaskStatus::Completed)
            ->pluck('workpackage_slug')
            ->unique()
            ->all();

        return match ($join) {
            'all' => collect($dependencies)
                ->every(
                    fn (string $slug): bool => in_array($slug, $completed, true)
                ),

            'any' => collect($dependencies)
                ->contains(
                    fn (string $slug): bool => in_array($slug, $completed, true)
                ),

            default => false,
        };
    }
}
