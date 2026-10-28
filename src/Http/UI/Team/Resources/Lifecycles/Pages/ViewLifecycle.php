<?php

// Pages/ViewLifecycle.php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Lifecycles\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\LifecycleResource;

class ViewLifecycle extends ViewRecord
{
    protected static string $resource = LifecycleResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
