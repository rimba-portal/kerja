<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\OfferingExecutions\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Team\Resources\OfferingExecutions\OfferingExecutionResource;

class ListOfferingExecutions extends ListRecords
{
    protected static string $resource = OfferingExecutionResource::class;
}
