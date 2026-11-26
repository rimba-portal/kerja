<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\AssetResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Team\Resources\AssetResource;

class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
