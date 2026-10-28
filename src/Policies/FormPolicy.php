<?php

declare(strict_types=1);

namespace Rimba\Work\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\Form;
use Rimba\Work\Services\WorkAccessService;

final readonly class FormPolicy
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
        Form $form,
    ): bool {
        return $form->orgUnit !== null
            && $this->workAccessService->canManageUnit(
                $user,
                $form->orgUnit,
            );
    }

    public function create(
        Authenticatable $user,
    ): bool {
        return $this->viewAny($user);
    }

    public function update(
        Authenticatable $user,
        Form $form,
    ): bool {
        return $this->view($user, $form);
    }

    public function delete(
        Authenticatable $user,
        Form $form,
    ): bool {
        return $this->view($user, $form);
    }
}
