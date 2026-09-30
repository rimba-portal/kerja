<?php

namespace Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkPackages extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\WorkPackages\WorkPackageResource::class;

    protected static ?string $title = 'Work Packages';

    protected ?string $subheading = 'Standardize grouping criteria for related projects and task blocks.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
