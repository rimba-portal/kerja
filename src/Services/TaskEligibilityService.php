<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\Task;

final class TaskEligibilityService
{
    public function __construct(private StaffJobRoleService $staffJobRoleService) {}

    public function isEligible(Model $actor, Task $task): bool
    {
        return $this->staffJobRoleService
            ->ids($actor)
            ->contains($task->package->actor_job_role_id);
    }
}
