<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Team\Resources\ProcessResource;

class ListProcesses extends ListRecords
{
    protected static string $resource = ProcessResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
