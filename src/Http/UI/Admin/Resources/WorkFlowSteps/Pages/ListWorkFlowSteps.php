<?php

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowSteps\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkFlowSteps extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\WorkFlowSteps\WorkFlowStepResource::class;

    protected static ?string $title = 'Workflow Process Steps';

    protected ?string $subheading = 'Plan sequential workflow steps, user actions, and rule dependencies.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
