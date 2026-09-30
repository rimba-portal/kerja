<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\WorkPackagePartyResource;

class ListWorkPackagePartys extends ListRecords
{
    protected static string $resource = WorkPackagePartyResource::class;

    protected static ?string $title = 'Work Package Participants';

    protected ?string $subheading = 'Link staff members, external partners, or roles to specific packages.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
