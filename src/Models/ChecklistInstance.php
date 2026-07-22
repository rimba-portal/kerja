<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'work_package_instance_id',
    'checklist_id',
    'status',
    'activated_at',
    'completed_at',
])]
class ChecklistInstance extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'work_package_instance_id' => 'integer',
            'checklist_id' => 'integer',
            'activated_at' => 'timestamp',
            'completed_at' => 'timestamp',
        ];
    }

    public function workPackageInstance(): BelongsTo
    {
        return $this->belongsTo(WorkPackageInstance::class);
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }
}
