<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\Task;

class TaskInboxService
{
    public function queryFor(
        Authenticatable&Model $subject
    ) {
        return Task::query()
            ->with('workflowInstance')
            ->human()
            ->open()
            ->whereMorphedTo('assignee', $subject)
            ->latest('assigned_at');
    }
}
