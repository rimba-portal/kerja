<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowSteps\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\WorkFlowSteps\WorkFlowStepResource;

class ListWorkFlowSteps extends ListRecords
{
    protected static string $resource = WorkFlowStepResource::class;

    protected static ?string $title = 'Workflow Process Steps';

    protected ?string $subheading = 'Plan sequential workflow steps, user actions, and rule dependencies.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
