<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Table('work_transitions')]
#[Fillable([
    'workflow_instance_id',
    'from_task_id',
    'to_task_id',
    'from_workpackage_slug',
    'to_workpackage_slug',
    'event',
    'actor_type',
    'actor_id',
    'payload',
    'metadata',
    'performed_at',
])]
class Transition extends Model
{
    protected static function booted(): void
    {
        static::creating(function (self $transition): void {
            $transition->performed_at ??= now();
        });
    }

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'metadata' => 'array',
            'performed_at' => 'datetime',
        ];
    }

    public function workflowInstance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function fromTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'from_task_id');
    }

    public function toTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'to_task_id');
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }
}
