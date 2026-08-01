<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Checklists\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Admin\Resources\Checklists\ChecklistResource;

class ViewChecklist extends ViewRecord
{
    protected static string $resource = ChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
