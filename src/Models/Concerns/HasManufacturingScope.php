<?php

declare(strict_types=1);

// src/Models/Concerns/HasManufacturingScope.php

namespace Rimba\Work\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Work\Models\Equipment;
use Rimba\Work\Models\Process;
use Rimba\Work\Models\ProcessHierarchyScope;

trait HasManufacturingScope
{
    public function equipment(): HasMany
    {
        return $this->hasMany(
            Equipment::class,
            'hierarchy_scope_id',
        );
    }

    public function processHierarchyScopes(): HasMany
    {
        return $this->hasMany(
            ProcessHierarchyScope::class,
            'hierarchy_scope_id',
        );
    }

    public function processes(): BelongsToMany
    {
        return $this->belongsToMany(
            Process::class,
            'process_hierarchy_scopes',
            'hierarchy_scope_id',
            'process_id',
        )
            ->withPivot([
                'id',
                'scope_level',
                'description',
                'attributes',
            ])
            ->withTimestamps();
    }
}
