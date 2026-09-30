<?php

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowInitiators\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkFlowInitiators extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\WorkFlowInitiators\WorkFlowInitiatorResource::class;

    protected static ?string $title = 'Workflow Initiators';

    protected ?string $subheading = 'Authorize specific teams or organizational groups to launch workflows.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
