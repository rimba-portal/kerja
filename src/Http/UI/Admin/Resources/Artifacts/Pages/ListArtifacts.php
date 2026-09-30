<?php

namespace Rimba\Work\Http\UI\Admin\Resources\Artifacts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtifacts extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\Artifacts\ArtifactResource::class;

    protected static ?string $title = 'Work Artifacts';

    protected ?string $subheading = 'Manage physical outputs, upload objects, and deliverable items.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
