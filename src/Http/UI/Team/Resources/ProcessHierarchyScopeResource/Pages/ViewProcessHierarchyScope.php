<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopeResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopeResource;

class ViewProcessHierarchyScope extends ViewRecord
{
    protected static string $resource = ProcessHierarchyScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
