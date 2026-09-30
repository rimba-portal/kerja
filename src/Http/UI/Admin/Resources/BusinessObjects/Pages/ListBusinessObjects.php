<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\BusinessObjects\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\BusinessObjects\BusinessObjectResource;

class ListBusinessObjects extends ListRecords
{
    protected static string $resource = BusinessObjectResource::class;

    protected static ?string $title = 'Linked Business Objects';

    protected ?string $subheading = 'Review associated core enterprise objects tied to operational workflows.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
