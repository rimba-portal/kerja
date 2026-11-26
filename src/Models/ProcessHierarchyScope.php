<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'process_id' => 'integer',
            'hierarchy_scope_id' => 'integer',
            'attributes' => 'array',
        ];
    }

    /** @return BelongsTo<Process, $this> */
    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function hierarchyScope(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'hierarchy_scope_id');
    }
}
