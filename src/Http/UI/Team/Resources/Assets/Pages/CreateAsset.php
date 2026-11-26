<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Assets\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Work\Http\UI\Team\Resources\Assets\AssetResource;

final class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;
}
