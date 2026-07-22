<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use App\Models\User;
use Rimba\Work\Models\ChecklistInstance;

final class ChecklistInstancePolicy
{
    public function view(
        User $user,
        ChecklistInstance $instance,
    ): bool {
        return $user->can('checklist-instance.view');
    }
}
