<?php

// Pages/ListLifecycles.php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Lifecycles\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\LifecycleResource;

class ListLifecycles extends ListRecords
{
    protected static string $resource = LifecycleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
