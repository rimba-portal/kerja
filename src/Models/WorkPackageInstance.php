<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'work_package_id',
    'status',
    'started_at',
    'completed_at',
])]
class WorkPackageInstance extends Model
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
            'workflow_instance_id' => 'integer',
            'started_at' => 'timestamp',
            'completed_at' => 'timestamp',
        ];
    }

    // public function workPackage(): BelongsTo
    // {
    //     return $this->belongsTo(WorkPackage::class);
    // }

    public function checklistInstances(): HasMany
    {
        return $this->hasMany(ChecklistInstance::class);
    }

    public function taskInstances(): HasMany
    {
        return $this->hasMany(TaskInstance::class);
    }
}
