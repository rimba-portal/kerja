<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkflowDefinitions\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Team\Resources\WorkflowDefinitions\WorkflowDefinitionResource;

class ListWorkflowDefinitions extends ListRecords
{
    protected static string $resource =
        WorkflowDefinitionResource::class;

    protected static ?string $title =
        'SIPOC Workflow Studio';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
