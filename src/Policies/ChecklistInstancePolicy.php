<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use App\Models\User;

final class ChecklistInstancePolicy
{
    public function view(
        User $user,
    ): bool {
        return $user->can('checklist-instance.view');
    }
}
