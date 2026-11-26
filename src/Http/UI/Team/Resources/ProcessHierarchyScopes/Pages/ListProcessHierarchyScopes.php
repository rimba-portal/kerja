<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\ProcessHierarchyScopeResource;

final class ListProcessHierarchyScopes extends ListRecords
{
    protected static string $resource = ProcessHierarchyScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
