<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Offerings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Team\Resources\Offerings\OfferingResource;

class ViewOffering extends ViewRecord
{
    protected static string $resource =
        OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
