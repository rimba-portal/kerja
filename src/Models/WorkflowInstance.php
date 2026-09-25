<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Rimba\Work\Enums\WorkflowStatus;

class WorkflowInstance extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $instance): void {
            $instance->uuid ??= (string) Str::uuid();
        });
    }

    public function getTable(): string
    {
        return config(
            'sipoc.tables.workflow_instances',
            'sipoc_workflow_instances'
        );
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
        return $this->hasMany(
            config('bites.sipoc.models.task', Task::class),
            'workflow_instance_id'
        );
    }

    public function artifacts(): HasMany
    {
        return $this->hasMany(
            config('bites.sipoc.models.artifact', Artifact::class),
            'workflow_instance_id'
        );
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(
            config('bites.sipoc.models.transition', Transition::class),
            'workflow_instance_id'
        );
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', WorkflowStatus::Active);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [
            WorkflowStatus::Completed,
            WorkflowStatus::Cancelled,
            WorkflowStatus::Failed,
        ], true);
    }
}
