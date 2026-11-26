<?php

declare(strict_types=1);

// src/Models/Process.php

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Floorplan\Models\Location;

#[Fillable([
    'name',
    'code',
    'description',
    'attributes',
])]
#[Table(name: 'processes')]
class Process extends Model
{
    protected function casts(): array
    {
        return [
            'attributes' => 'array',
        ];
    }

    public function hierarchyScopes(): HasMany
    {
        return $this->hasMany(
            ProcessHierarchyScope::class,
            'process_id',
        );
    }

    public function workUnits(): BelongsToMany
    {
        return $this->belongsToMany(
            Location::class,
            'process_hierarchy_scopes',
            'process_id',
            'hierarchy_scope_id',
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
