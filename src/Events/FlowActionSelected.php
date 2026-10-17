<?php

declare(strict_types=1);

namespace Rimba\Work\Events;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\FlowAction;
use Rimba\Work\Models\Task;

final readonly class FlowActionSelected
{
    public function __construct(public Task $task, public FlowAction $flowAction, public Model $actor) {}
}
