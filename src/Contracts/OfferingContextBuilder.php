<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Rimba\Work\Data\OfferingContext;
use Rimba\Work\Models\Offering;

interface OfferingContextBuilder
{
    public function build(
        Offering $offering,
        array $metadata = [],
    ): OfferingContext;
}
