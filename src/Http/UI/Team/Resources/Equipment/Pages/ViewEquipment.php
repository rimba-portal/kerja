<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Equipment\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Team\Resources\Equipment\EquipmentResource;

final class ViewEquipment extends ViewRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
