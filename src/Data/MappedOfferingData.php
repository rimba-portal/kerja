<?php

declare(strict_types=1);

namespace Rimba\Work\Data;

final readonly class MappedOfferingData
{
    public function __construct(
        public array $attributes,
        public array $input,
        public array $context,
    ) {}
}
