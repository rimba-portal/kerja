<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Floorplan\Models\Location;

#[Fillable([
    'hierarchy_scope_id',
    'name',
    'code',
    'manufacturer',
    'model',
    'serial_number',
    'state',
    'description',
    'attributes',
])]
#[Table(name: 'equipment')]
class Equipment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'hierarchy_scope_id' => 'integer',
            'attributes' => 'array',
        ];
    }

    /** @return BelongsTo<Location, $this> */
    public function hierarchyScope(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'hierarchy_scope_id');
    }

    /** @return HasMany<Asset, $this> */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
