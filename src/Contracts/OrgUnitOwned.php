<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Rimba\Organization\Models\OrgUnit;

interface OrgUnitOwned
{
    public function owningOrgUnit(): ?OrgUnit;
}
