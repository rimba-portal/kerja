<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class WorkflowPolicy
{
    public function viewAny(Authenticatable $u): bool
    {
        return $u->can('view.workflow');
    }

    public function start(Authenticatable $u): bool
    {
        return $u->can('start.workflow');
    }
}
