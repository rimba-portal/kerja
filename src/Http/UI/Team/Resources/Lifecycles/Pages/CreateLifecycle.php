<?php

// Pages/CreateLifecycle.php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Lifecycles\Pages;

use Filament\Resources\Pages\CreateRecord;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\LifecycleResource;

class CreateLifecycle extends CreateRecord
{
    protected static string $resource = LifecycleResource::class;
}
