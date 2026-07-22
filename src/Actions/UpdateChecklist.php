<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\Checklist;

final class UpdateChecklist
{
    public function execute(
        Checklist $checklist,
        array $attributes,
    ): Checklist {

        $checklist->update($attributes);

        return $checklist->refresh();
    }
}
