<?php

// Pages/EditLifecycle.php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Lifecycles\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\LifecycleResource;

class EditLifecycle extends EditRecord
{
    protected static string $resource = LifecycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
