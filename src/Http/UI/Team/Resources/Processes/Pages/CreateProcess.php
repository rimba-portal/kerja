<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Processes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Work\Http\UI\Team\Resources\Processes\ProcessResource;

final class CreateProcess extends CreateRecord
{
    protected static string $resource = ProcessResource::class;
}
