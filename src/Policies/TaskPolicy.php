<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class TaskPolicy
{
    public function act(Authenticatable $u): bool
    {
        return $u->can('act.task');
    }
}
