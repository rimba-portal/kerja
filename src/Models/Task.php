<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Rimba\Work\Enums\TaskStatus;

#[Table(name: 'work_tasks')]
#[Unguarded]
class Task extends Model
{
    protected static function booted(): void
    {
        static::creating(
            fn (self $task) => $task->uuid ??= (string) Str::uuid()
        );
    }

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'payload' => 'array',
            'result' => 'array',
            'work_package_version' => 'integer',
            'assigned_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'due_at' => 'datetime',
        ];
    }

    public function flowInstance(): BelongsTo
    {
        return $this->belongsTo(
            FlowInstance::class,
            'workflow_instance_id',
        );
    }

    public function flowStep(): BelongsTo
    {
        return $this->belongsTo(
            FlowStep::class,
            'workflow_step_id',
        );
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(
            Package::class,
            'work_package_id',
        );
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo();
    }
}
