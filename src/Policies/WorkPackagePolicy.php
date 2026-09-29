<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;

final class WorkPackagePolicy
{
    public function viewAny(Authenticatable $u): bool
    {
        return $u->can('view.workpackage');
    }

    public function update(Authenticatable $u): bool
    {
        return $u->can('update.workpackage');
    }
}
