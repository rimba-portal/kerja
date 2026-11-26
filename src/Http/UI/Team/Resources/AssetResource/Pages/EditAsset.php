<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\AssetResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Rimba\Work\Http\UI\Team\Resources\AssetResource;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [ViewAction::make(), DeleteAction::make()];
    }
}
