<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Core\Models\Role;
use Rimba\Work\Models\Checklist;
use Rimba\Work\Models\Task;

final class CreateTask
{
    public function execute(
        Checklist $checklist,
        Role $role,
        string $description,
        bool $active = true,
    ): Task {

        return $checklist
            ->tasks()
            ->create([
                'role_id' => $role->id,
                'description' => $description,
                'active' => $active,
            ]);
    }
}
