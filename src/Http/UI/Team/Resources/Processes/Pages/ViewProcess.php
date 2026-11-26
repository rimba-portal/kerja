<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Processes\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Team\Resources\Processes\ProcessResource;

final class ViewProcess extends ViewRecord
{
    protected static string $resource = ProcessResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
