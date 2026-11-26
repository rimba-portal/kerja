<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\ProcessHierarchyScopeResource;

final class ViewProcessHierarchyScope extends ViewRecord
{
    protected static string $resource = ProcessHierarchyScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
