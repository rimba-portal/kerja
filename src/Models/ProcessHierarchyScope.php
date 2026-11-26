<?php

declare(strict_types=1);

// src/Models/ProcessHierarchyScope.php

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rimba\Floorplan\Models\Location;

#[Fillable([
    'process_id',
    'hierarchy_scope_id',
    'scope_level',
    'description',
    'attributes',
])]
#[Table(name: 'process_hierarchy_scopes')]
class ProcessHierarchyScope extends Model
{
    protected function casts(): array
    {
        return [
            'attributes' => 'array',
        ];
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(
            Process::class,
            'process_id',
        );
    }

    public function hierarchyScope(): BelongsTo
    {
        return $this->belongsTo(
            Location::class,
            'hierarchy_scope_id',
        );
    }
}
