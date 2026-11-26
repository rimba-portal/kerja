<?php

declare(strict_types=1);

// src/Models/Equipment.php

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Floorplan\Models\Location;

#[Fillable([
    'hierarchy_scope_id',
    'name',
    'code',
    'description',
    'attributes',
])]
#[Table(name: 'equipment')]
class Equipment extends Model
{
    protected function casts(): array
    {
        return [
            'attributes' => 'array',
        ];
    }

    public function hierarchyScope(): BelongsTo
    {
        return $this->belongsTo(
            Location::class,
            'hierarchy_scope_id',
        );
    }

    public function assets(): HasMany
    {
        return $this->hasMany(
            Asset::class,
            'equipment_id',
        );
    }
}
