<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\TaskEvents\TaskEventResource;

class ListTaskEvents extends ListRecords
{
    protected static string $resource = TaskEventResource::class;

    protected static ?string $title = 'Task Event Logs';

    protected ?string $subheading =
        'Track chronological operational updates, adjustments, and milestone steps.';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
