<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use App\Trees\Organization\Models\Staff;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'checklist_instance_id',
    'task_id',
    'assigned_to_id',
    'completed_by_id',
    'is_completed',
    'completed_at',
])]
class TaskInstance extends Model
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
            'checklist_instance_id' => 'integer',
            'task_id' => 'integer',
            'assigned_to_id' => 'integer',
            'completed_by_id' => 'integer',
            'is_completed' => 'boolean',
            'completed_at' => 'timestamp',
        ];
    }

    public function workPackageInstance(): BelongsTo
    {
        return $this->belongsTo(WorkPackageInstance::class);
    }

    public function checklistInstance(): BelongsTo
    {
        return $this->belongsTo(ChecklistInstance::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
