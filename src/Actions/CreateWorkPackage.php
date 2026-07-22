<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\WorkPackage;

final class CreateWorkPackage
{
    public function execute(
        string $name,
        ?string $description = null,
        bool $active = true,
    ): WorkPackage {
        return WorkPackage::create([
            'name' => $name,
            'description' => $description,
            'active' => $active,
        ]);
    }
}
