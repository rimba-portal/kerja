<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Rimba\Work\Contracts\OfferingAuthorizer;
use Rimba\Work\Models\Offering;

class OfferingPolicyAuthorizer implements OfferingAuthorizer
{
    public function authorize(
        Offering $offering,
        ?Authenticatable $user,
    ): void {
        Gate::forUser($user)->authorize(
            'execute',
            $offering,
        );
    }
}
