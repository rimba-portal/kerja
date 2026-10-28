<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\Form;

class FormPolicy
{
    public function viewAny(
        Authenticatable $user,
    ): bool {
        return $user->can(
            'work.form.view-any',
        );
    }

    public function view(
        Authenticatable $user,
        Form $form,
    ): bool {
        return $user->can(
            'work.form.view',
        );
    }

    public function create(
        Authenticatable $user,
    ): bool {
        return $user->can(
            'work.form.create',
        );
    }

    public function update(
        Authenticatable $user,
        Form $form,
    ): bool {
        return $user->can(
            'work.form.update',
        );
    }

    public function delete(
        Authenticatable $user,
        Form $form,
    ): bool {
        return $user->can(
            'work.form.delete',
        );
    }
}
