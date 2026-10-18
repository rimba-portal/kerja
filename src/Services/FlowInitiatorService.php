<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\Flow;

final class FlowInitiatorService
{
    public function __construct(private StaffJobRoleService $staffJobRoleService) {}

    public function mayStart(Model $s, Flow $w): bool
    {
        return $w->initiators()->whereIn('job_role_id', $this->staffJobRoleService->ids($s))->exists();
    }

    public function availableFor(Model $s)
    {
        return Flow::query()
            ->where('status', 'active')
            ->whereHas('initiators', fn ($q) => $q->whereIn('job_role_id', $this->staffJobRoleService->ids($s)));
    }
}
