<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Rimba\Work\Enums\ArtifactType;

#[Table('work_artifacts')]
#[Fillable([
    'uuid',
    'workflow_instance_id',
    'produced_by_task_id',
    'key',
    'name',
    'type',
    'value',
    'record_type',
    'record_id',
    'metadata',
    'produced_at',
])]
class Artifact extends Model
{
    protected static function booted(): void
    {
        static::creating(function (self $artifact): void {
            $artifact->uuid ??= (string) Str::uuid();
            $artifact->produced_at ??= now();
        });
    }

    protected function casts(): array
    {
        return [
            'type' => ArtifactType::class,
            'value' => 'array',
            'metadata' => 'array',
            'produced_at' => 'datetime',
        ];
    }

    public function workflowInstance(): BelongsTo
    {
        return $this->belongsTo(WorkflowInstance::class, 'workflow_instance_id');
    }

    public function producedByTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'produced_by_task_id');
    }

    public function record(): MorphTo
    {
        return $this->morphTo();
    }
}
