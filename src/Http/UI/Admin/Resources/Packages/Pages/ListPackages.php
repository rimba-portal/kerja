<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Packages\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\Packages\PackageResource;

class ListPackages extends ListRecords
{
    protected static string $resource = PackageResource::class;

    protected static ?string $title = 'Work Packages';

    protected ?string $subheading = 'Standardize grouping criteria for related projects and task blocks.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
