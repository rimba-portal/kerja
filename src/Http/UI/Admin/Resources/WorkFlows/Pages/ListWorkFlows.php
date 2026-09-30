<?php

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlows\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkFlows extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\WorkFlows\WorkFlowResource::class;

    protected static ?string $title = 'Core Workflows';

    protected ?string $subheading = 'View operational workflow templates, configurations, and baseline properties.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
