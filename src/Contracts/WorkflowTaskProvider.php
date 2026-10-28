<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface WorkflowTaskProvider
{
    public function assignedQuery(
        Authenticatable $user,
    ): Builder;

    public function teamQuery(
        Model $team,
    ): Builder;

    public function actions(
        Model $task,
        Authenticatable $user,
    ): array;

    public function perform(
        Model $task,
        string $action,
        Authenticatable $user,
        array $data = [],
    ): void;
}
