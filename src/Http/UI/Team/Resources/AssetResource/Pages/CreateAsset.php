<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\AssetResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Work\Http\UI\Team\Resources\AssetResource;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;
}
