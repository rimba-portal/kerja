<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\FlowInstances\Pages;

use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Admin\Resources\FlowInstances\FlowInstanceResource;

class ListFlowInstances extends ListRecords
{
    protected static string $resource = FlowInstanceResource::class;

    protected static ?string $title = 'Workflow Instances';

    protected ?string $subheading =
        'Track workflow executions and their runtime status.';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
