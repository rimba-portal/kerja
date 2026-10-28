<?php

declare(strict_types=1);

namespace Rimba\Work\Data;

use Illuminate\Database\Eloquent\Model;

final readonly class FormDefinition
{
    public function __construct(
        public string $provider,
        public string $key,
        public string $name,
        public array $definition = [],
        public ?Model $providerModel = null,
    ) {}
}
