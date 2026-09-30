<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowInstances\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\WorkFlowInstances\WorkFlowInstanceResource;

class ListWorkFlowInstances extends ListRecords
{
    protected static string $resource = WorkFlowInstanceResource::class;

    protected static ?string $title = 'Active Workflow Instances';

    protected ?string $subheading = 'Track active process runs, execution tracks, and operational run statuses.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
