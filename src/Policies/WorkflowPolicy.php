<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\Workflow;
use Rimba\Work\Services\WorkAccessService;

final readonly class WorkflowPolicy
{
    public function __construct(
        private WorkAccessService $workAccessService,
    ) {}

    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $this->workAccessService
            ->manageableUnitIds($user) !== [];
    }

    public function view(
        Authenticatable $user,
        Workflow $workflow,
    ): bool {
        return $workflow->orgUnit !== null
            && $this->workAccessService->canManageUnit(
                $user,
                $workflow->orgUnit,
            );
    }

    public function create(
        Authenticatable $user,
    ): bool {
        return $this->viewAny($user);
    }

    public function update(
        Authenticatable $user,
        Workflow $workflow,
    ): bool {
        return $this->view(
            $user,
            $workflow,
        );
    }

    public function delete(
        Authenticatable $user,
        Workflow $workflow,
    ): bool {
        return $this->view(
            $user,
            $workflow,
        );
    }
}
