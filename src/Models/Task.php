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
        static::creating(fn (self $task) => $task->uuid ??= (string) Str::uuid());
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
        return $this->belongsTo(FlowInstance::class);
    }

    public function flowStep(): BelongsTo
    {
        return $this->belongsTo(FlowStep::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo();
    }
}
