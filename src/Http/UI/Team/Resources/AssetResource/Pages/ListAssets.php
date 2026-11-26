<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\AssetResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Team\Resources\AssetResource;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
