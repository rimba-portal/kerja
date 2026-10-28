<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\Workflow;

class WorkflowPolicy
{
    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $user->can(
            'work.workflow.view-any',
        );
    }

    public function view(
        Authenticatable $user,
        Workflow $workflow,
    ): bool {
        return $user->can(
            'work.workflow.view',
        );
    }

    public function create(
        Authenticatable $user,
    ): bool {
        return $user->can(
            'work.workflow.create',
        );
    }

    public function update(
        Authenticatable $user,
        Workflow $workflow,
    ): bool {
        return $user->can(
            'work.workflow.update',
        );
    }

    public function delete(
        Authenticatable $user,
        Workflow $workflow,
    ): bool {
        return $user->can(
            'work.workflow.delete',
        );
    }
}
