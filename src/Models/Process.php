<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'attributes' => 'array',
        ];
    }

    /** @return HasMany<ProcessHierarchyScope, $this> */
    public function scopeAssignments(): HasMany
    {
        return $this->hasMany(ProcessHierarchyScope::class);
    }

    /** @return BelongsToMany<Location, $this> */
    public function hierarchyScopes(): BelongsToMany
    {
        return $this->belongsToMany(
            Location::class,
            'process_hierarchy_scopes',
            'process_id',
            'hierarchy_scope_id'
        )->withPivot(['scope_level', 'description', 'attributes'])
            ->withTimestamps();
    }
}
