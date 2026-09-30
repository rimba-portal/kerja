<?php

namespace Rimba\Work\Http\UI\Admin\Resources\WorkPackagePayloads\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkPackagePayloads extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\WorkPackagePayloads\WorkPackagePayloadResource::class;

    protected static ?string $title = 'Work Package Payloads';

    protected ?string $subheading = 'Store operational database values and core parameters for context routing.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
