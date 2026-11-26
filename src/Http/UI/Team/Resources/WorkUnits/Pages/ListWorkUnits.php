<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkUnits\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Team\Resources\WorkUnits\WorkUnitResource;

final class ListWorkUnits extends ListRecords
{
    protected static string $resource = WorkUnitResource::class;
}
