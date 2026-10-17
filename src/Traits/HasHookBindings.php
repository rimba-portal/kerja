<?php

declare(strict_types=1);

namespace Rimba\Work\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rimba\Work\Models\HookBinding;

trait HasHookBindings
{
    public function hookBindings(): MorphMany
    {
        return $this->morphMany(HookBinding::class, 'hookable')
            ->orderBy('hook_point')
            ->orderBy('sequence');
    }
}
