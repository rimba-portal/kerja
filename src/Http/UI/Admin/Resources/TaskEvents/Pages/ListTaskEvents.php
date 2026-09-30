<?php

namespace Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaskEvents extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\TaskEvents\TaskEventResource::class;

    protected static ?string $title = 'Task Event Logs';

    protected ?string $subheading = 'Track chronological operational updates, adjustments, and milestone steps.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
