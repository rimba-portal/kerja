<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\ProcessHierarchyScopeResource;

final class CreateProcessHierarchyScope extends CreateRecord
{
    protected static string $resource = ProcessHierarchyScopeResource::class;
}
