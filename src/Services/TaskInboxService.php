<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\Task;

final class TaskInboxService
{
    public function __construct(private StaffJobRoleService $staffJobRoleService) {}

    public function queryFor(Model $s)
    {
        return Task::query()->whereIn('status', ['ready', 'assigned', 'started'])->whereHas('workPackage', fn ($q) => $q->whereIn('actor_job_role_id', $this->staffJobRoleService->ids($s)));
    }
}
