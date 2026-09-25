<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Rimba\Work\Enums\ActivityType;
use Rimba\Work\Enums\ExecutionType;
use Rimba\Work\Enums\TaskStatus;

class Task extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $task): void {
            $task->uuid ??= (string) Str::uuid();

            $task->name ??= trim(implode(' ', array_filter([
                $task->actor,
                $task->activity_type instanceof ActivityType
                    ? $task->activity_type->label()
                    : str((string) $task->activity_type)->headline(),
                $task->business_object,
            ])));
        });
    }

    public function getTable(): string
    {
        return config('bites.sipoc.tables.tasks', 'sipoc_tasks');
    }

    protected function casts(): array
    {
        return [
            'workflow_instance_id' => 'integer',
            'workpackage_snapshot' => 'array',
            'execution_type' => ExecutionType::class,
            'activity_type' => ActivityType::class,
            'status' => TaskStatus::class,
            'sequence' => 'integer',
            'attempt' => 'integer',
            'suppliers' => 'array',
            'inputs' => 'array',
            'outputs' => 'array',
            'customers' => 'array',
            'payload' => 'array',
            'result' => 'array',
            'ready_at' => 'datetime',
            'assigned_at' => 'datetime',
            'started_at' => 'datetime',
            'waiting_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'failed_at' => 'datetime',
            'due_at' => 'datetime',
        ];
    }

    public function workflowInstance(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.sipoc.models.workflow_instance', WorkflowInstance::class),
            'workflow_instance_id'
        );
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo();
    }

    public function artifacts(): HasMany
    {
        return $this->hasMany(
            config('bites.sipoc.models.artifact', Artifact::class),
            'produced_by_task_id'
        );
    }

    #[Scope]
    protected function open(Builder $query): Builder
    {
        return $query->whereIn('status', [
            TaskStatus::Pending,
            TaskStatus::Ready,
            TaskStatus::Assigned,
            TaskStatus::Started,
            TaskStatus::Waiting,
        ]);
    }

    #[Scope]
    protected function human(Builder $query): Builder
    {
        return $query->where(
            'execution_type',
            ExecutionType::Human
        );
    }

    #[Scope]
    protected function assignedTo(
        Builder $query,
        Model $assignee
    ): Builder {
        return $query->whereMorphedTo('assignee', $assignee);
    }
}
