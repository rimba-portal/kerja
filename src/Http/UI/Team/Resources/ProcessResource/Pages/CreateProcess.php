<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Work\Http\UI\Team\Resources\ProcessResource;

class CreateProcess extends CreateRecord
{
    protected static string $resource = ProcessResource::class;
}
