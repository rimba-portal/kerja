<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Checklists\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Work\Http\UI\Admin\Resources\Checklists\ChecklistResource;

class CreateChecklist extends CreateRecord
{
    protected static string $resource = ChecklistResource::class;
}
