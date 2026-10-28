<?php

declare(strict_types=1);

namespace Rimba\Work\Registries;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Contracts\SubjectRegistry as SubjectRegistryContract;
use Rimba\Work\Exceptions\InvalidSubjectException;
use Rimba\Work\Exceptions\SubjectResolutionException;

class SubjectRegistry implements SubjectRegistryContract
{
    /**
     * @var array<string, class-string<Model>>
     */
    protected array $subjects = [];

    public function register(
        string $alias,
        string $modelClass,
    ): static {
        if (blank($alias)) {
            throw new InvalidSubjectException(
                'The subject alias cannot be empty.'
            );
        }

        if (! is_a($modelClass, Model::class, true)) {
            throw new InvalidSubjectException(
                "Subject [{$alias}] must resolve to an Eloquent model."
            );
        }

        $this->subjects[$alias] = $modelClass;

        return $this;
    }

    public function resolve(string $alias): string
    {
        return $this->subjects[$alias]
            ?? throw new SubjectResolutionException(
                "No subject is registered for alias [{$alias}]."
            );
    }

    public function has(string $alias): bool
    {
        return array_key_exists(
            $alias,
            $this->subjects,
        );
    }

    public function aliasFor(
        string|Model $model,
    ): ?string {
        $modelClass = $model instanceof Model
            ? $model::class
            : $model;

        $alias = array_search(
            $modelClass,
            $this->subjects,
            true,
        );

        return $alias !== false
            ? $alias
            : null;
    }

    public function all(): array
    {
        return $this->subjects;
    }
}
