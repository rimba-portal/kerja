<?php

declare(strict_types=1);

// src/Models/Asset.php

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'equipment_id',
    'name',
    'code',
    'description',
    'attributes',
])]
#[Table(name: 'assets')]
class Asset extends Model
{
    protected function casts(): array
    {
        return [
            'attributes' => 'array',
        ];
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(
            Equipment::class,
            'equipment_id',
        );
    }
}
