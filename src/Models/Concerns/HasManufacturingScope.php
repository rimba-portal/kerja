<?php

declare(strict_types=1);

namespace Rimba\Work\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Work\Models\Equipment;
use Rimba\Work\Models\Process;
use Rimba\Work\Models\ProcessHierarchyScope;

trait HasManufacturingScope
{
    /** @return HasMany<Equipment, $this> */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'hierarchy_scope_id');
    }

    /** @return HasMany<ProcessHierarchyScope, $this> */
    public function processHierarchyScopes(): HasMany
    {
        return $this->hasMany(ProcessHierarchyScope::class, 'hierarchy_scope_id');
    }

    /** @return BelongsToMany<Process, $this> */
    public function processes(): BelongsToMany
    {
        return $this->belongsToMany(
            Process::class,
            'process_hierarchy_scopes',
            'hierarchy_scope_id',
            'process_id'
        )->withPivot(['scope_level', 'description', 'attributes'])
            ->withTimestamps();
    }
}
