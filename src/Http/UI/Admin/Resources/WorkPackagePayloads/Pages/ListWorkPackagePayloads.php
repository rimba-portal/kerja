<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkPackagePayloads\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackagePayloads\WorkPackagePayloadResource;

class ListWorkPackagePayloads extends ListRecords
{
    protected static string $resource = WorkPackagePayloadResource::class;

    protected static ?string $title = 'Work Package Payloads';

    protected ?string $subheading = 'Store operational database values and core parameters for context routing.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
