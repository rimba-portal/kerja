<?php

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowFormFields\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkFlowFormFields extends ListRecords
{
    protected static string $resource = \Rimba\Work\Http\UI\Admin\Resources\WorkFlowFormFields\WorkFlowFormFieldResource::class;

    protected static ?string $title = 'Form Fields';

    protected ?string $subheading = 'Setup input field configurations, labels, validations, and parameters.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
