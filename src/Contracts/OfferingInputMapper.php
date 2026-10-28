<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Rimba\Work\Data\MappedOfferingData;
use Rimba\Work\Data\OfferingContext;
use Rimba\Work\Models\Offering;

interface OfferingInputMapper
{
    public function map(
        Offering $offering,
        array $input,
        OfferingContext $context,
    ): MappedOfferingData;
}
