<?php

declare(strict_types=1);

namespace Rimba\Work\Data;

use Illuminate\Database\Eloquent\Model;

final readonly class WorkflowDefinition
{
    public function __construct(
        public string $provider,
        public string $key,
        public string $name,
        public bool $active,
        public array $definition = [],
        public ?Model $providerModel = null,
    ) {}
}
