<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Rimba\Work\Enums\WorkflowStatus;

#[Table('work_workflow_instances')]
#[Fillable([
    'uuid',
    'workflow_slug',
    'workflow_version',
    'definition_snapshot',
    'subject_type',
    'subject_id',
    'initiator_type',
    'initiator_id',
    'current_workpackage_slug',
    'status',
    'context',
    'started_at',
    'completed_at',
    'cancelled_at',
    'failed_at',
    'failure_reason',
])]
class WorkflowInstance extends Model
{
    protected static function booted(): void
    {
        static::creating(function (self $instance): void {
            $instance->uuid ??= (string) Str::uuid();
        });
    }

    protected function casts(): array
    {
        return [
            'workflow_version' => 'integer',
            'definition_snapshot' => 'array',
            'status' => WorkflowStatus::class,
            'context' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function initiator(): MorphTo
    {
        return $this->morphTo();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', WorkflowStatus::Active);
    }
}
