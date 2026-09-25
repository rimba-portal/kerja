<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Rimba\Work\Enums\ArtifactType;

class Artifact extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $artifact): void {
            $artifact->uuid ??= (string) Str::uuid();
            $artifact->produced_at ??= now();
        });
    }

    public function getTable(): string
    {
        return config('bites.sipoc.tables.artifacts', 'sipoc_artifacts');
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
        return $this->belongsTo(
            config('bites.sipoc.models.workflow_instance', WorkflowInstance::class),
            'workflow_instance_id'
        );
    }

    public function producedByTask(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.sipoc.models.task', Task::class),
            'produced_by_task_id'
        );
    }

    public function record(): MorphTo
    {
        return $this->morphTo();
    }
}
