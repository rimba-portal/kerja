<?php

namespace Rimba\Work\Http\UI\Admin\Resources\ActivityTypes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActivityTypes extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\ActivityTypes\ActivityTypeResource::class;

    protected static ?string $title = 'Activity Types';

    protected ?string $subheading = 'Catalog operational task variants and system-wide automation categories.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
