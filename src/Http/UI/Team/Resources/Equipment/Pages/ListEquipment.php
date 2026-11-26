<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Equipment\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Team\Resources\Equipment\EquipmentResource;

final class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
