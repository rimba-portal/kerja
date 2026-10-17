<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\FlowActions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\FlowActions\FlowActionResource;

class ListFlowActions extends ListRecords
{
    protected static string $resource = FlowActionResource::class;

    protected static ?string $title = 'Workflow Core Actions';

    protected ?string $subheading = 'Configure specific interaction items and choices for task actions.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
