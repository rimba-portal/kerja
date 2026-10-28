<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Forms\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Rimba\Work\Http\UI\Team\Resources\Forms\FormResource;

class ViewForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
