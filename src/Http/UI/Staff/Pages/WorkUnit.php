<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Team\Resources\WorkUnitResource;

class WorkUnit extends ViewRecord
{
    protected static string $resource = WorkUnitResource::class;

    public function getTitle(): string
    {
        $code = $this->getRecord()->getAttribute('code');
        $name = $this->getRecord()->getAttribute('name');

        return trim("{$code} — {$name}", ' —');
    }
}
