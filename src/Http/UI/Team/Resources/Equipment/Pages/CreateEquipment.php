<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Equipment\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Work\Http\UI\Team\Resources\Equipment\EquipmentResource;

final class CreateEquipment extends CreateRecord
{
    protected static string $resource = EquipmentResource::class;
}
