<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Tasks\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\Tasks\TaskResource;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected static ?string $title = 'Task Inbox';

    protected ?string $subheading = 'Monitor individual items, task states, and staff action assignments.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
