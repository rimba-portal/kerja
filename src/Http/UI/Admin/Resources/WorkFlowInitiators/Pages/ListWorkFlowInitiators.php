<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowInitiators\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\WorkFlowInitiators\WorkFlowInitiatorResource;

class ListWorkFlowInitiators extends ListRecords
{
    protected static string $resource = WorkFlowInitiatorResource::class;

    protected static ?string $title = 'Workflow Initiators';

    protected ?string $subheading = 'Authorize specific teams or organizational groups to launch workflows.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
