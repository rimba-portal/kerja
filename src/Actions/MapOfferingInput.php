<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Contracts\OfferingInputMapper;
use Rimba\Work\Data\MappedOfferingData;
use Rimba\Work\Data\OfferingContext;
use Rimba\Work\Models\Offering;

class MapOfferingInput
{
    public function __construct(
        protected OfferingInputMapper $mapper,
    ) {}

    public function execute(
        Offering $offering,
        array $input,
        OfferingContext $context,
    ): MappedOfferingData {
        return $this->mapper->map(
            offering: $offering,
            input: $input,
            context: $context,
        );
    }
}
