<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Artifacts\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\Artifacts\ArtifactResource;

class ListArtifacts extends ListRecords
{
    protected static string $resource = ArtifactResource::class;

    protected static ?string $title = 'Work Artifacts';

    protected ?string $subheading =
        'Review outputs and records produced during workflow execution.';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
