<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Team\Resources\WorkUnits\WorkUnitResource;

final class WorkUnit extends ViewRecord
{
    protected static string $resource = WorkUnitResource::class;
}
