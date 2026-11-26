<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'equipment_id',
    'name',
    'code',
    'inventory_number',
    'main_asset_number',
    'asset_class',
    'description',
    'description2',
    'cost_center',
    'group',
    'serial_number',
    'attributes',
])]
#[Table(name: 'assets')]
class Asset extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'equipment_id' => 'integer',
            'attributes' => 'array',
        ];
    }

    /** @return BelongsTo<Equipment, $this> */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}
