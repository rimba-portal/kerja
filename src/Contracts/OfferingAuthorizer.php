<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Rimba\Work\Models\Offering;

interface OfferingAuthorizer
{
    public function authorize(
        Offering $offering,
        ?Authenticatable $user,
    ): void;
}
