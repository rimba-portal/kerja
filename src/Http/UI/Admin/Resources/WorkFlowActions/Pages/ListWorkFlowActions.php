<?php

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowActions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkFlowActions extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\WorkFlowActions\WorkFlowActionResource::class;

    protected static ?string $title = 'Workflow Core Actions';

    protected ?string $subheading = 'Configure specific interaction items and choices for task actions.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
