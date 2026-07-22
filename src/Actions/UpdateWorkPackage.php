<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\WorkPackage;

final class UpdateWorkPackage
{
    public function execute(
        WorkPackage $workPackage,
        array $attributes,
    ): WorkPackage {
        $workPackage->update($attributes);

        return $workPackage->refresh();
    }
}
