<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transition extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $transition): void {
            $transition->performed_at ??= now();
        });
    }

    public function getTable(): string
    {
        return config('bites.sipoc.tables.transitions', 'sipoc_transitions');
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
        return $this->belongsTo(
            config('bites.sipoc.models.workflow_instance', WorkflowInstance::class),
            'workflow_instance_id'
        );
    }

    public function fromTask(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.sipoc.models.task', Task::class),
            'from_task_id'
        );
    }

    public function toTask(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.sipoc.models.task', Task::class),
            'to_task_id'
        );
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }
}
