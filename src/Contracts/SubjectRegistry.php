<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Illuminate\Database\Eloquent\Model;

interface SubjectRegistry
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public function register(
        string $alias,
        string $modelClass,
    ): static;

    /**
     * @return class-string<Model>
     */
    public function resolve(string $alias): string;

    public function has(string $alias): bool;

    public function aliasFor(
        string|Model $model,
    ): ?string;

    /**
     * @return array<string, class-string<Model>>
     */
    public function all(): array;
}
