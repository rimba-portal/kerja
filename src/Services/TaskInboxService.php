<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Task;

final class TaskInboxService
{
    public function __construct(private StaffJobRoleService $staffJobRoleService) {}

    public function availableFor(Model $actor): Builder
    {
        $roleIds = $this->staffJobRoleService->ids($actor);

        return Task::query()
            ->where('status', TaskStatus::Ready)
            ->whereNull('assignee_id')
            ->whereHas('package', fn (Builder $q) => $q->whereIn('actor_job_role_id', $roleIds));
    }

    public function assignedTo(Model $actor): Builder
    {
        return Task::query()
            ->where('status', TaskStatus::Assigned)
            ->whereMorphedTo('assignee', $actor);
    }

    public function startedBy(Model $actor): Builder
    {
        return Task::query()
            ->where('status', TaskStatus::Started)
            ->whereMorphedTo('assignee', $actor);
    }

    public function queryFor(Model $actor): Builder
    {
        $roleIds = $this->staffJobRoleService->ids($actor);

        return Task::query()->where(function (Builder $query) use ($actor, $roleIds): void {
            $query
                ->where(function (Builder $available) use ($roleIds): void {
                    $available
                        ->where('status', TaskStatus::Ready)
                        ->whereNull('assignee_id')
                        ->whereHas('package', fn (Builder $package) => $package->whereIn('actor_job_role_id', $roleIds));
                })
                ->orWhere(function (Builder $mine) use ($actor): void {
                    $mine
                        ->whereIn('status', [TaskStatus::Assigned, TaskStatus::Started])
                        ->whereMorphedTo('assignee', $actor);
                });
        });
    }
}
