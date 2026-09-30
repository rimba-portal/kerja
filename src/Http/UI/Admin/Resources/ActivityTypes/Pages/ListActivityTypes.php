<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\ActivityTypes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\ActivityTypes\ActivityTypeResource;

class ListActivityTypes extends ListRecords
{
    protected static string $resource = ActivityTypeResource::class;

    protected static ?string $title = 'Activity Types';

    protected ?string $subheading = 'Catalog operational task variants and system-wide automation categories.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
