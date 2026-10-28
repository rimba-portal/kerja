<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\OfferingExecution;

class CancelOfferingExecution
{
    public function execute(
        OfferingExecution $execution,
    ): OfferingExecution {
        return $execution->markCancelled();
    }
}
