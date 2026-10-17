<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Rimba\Work\Enums\FlowInstanceStatus;

#[Table(name: 'work_flow_instances')]
#[Unguarded]
class FlowInstance extends Model
{
    protected static function booted(): void
    {
        static::creating(
            fn (self $instance) => $instance->uuid ??= (string) Str::uuid()
        );
    }

    protected function casts(): array
    {
        return [
            'status' => FlowInstanceStatus::class,
            'workflow_version' => 'integer',
            'form_data' => 'array',
            'traveller' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'withdrawn_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Flow::class);
    }

    public function currentFlowStep(): BelongsTo
    {
        return $this->belongsTo(
            FlowStep::class,
            'current_workflow_step_id'
        );
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'workflow_instance_id');
    }

    public function initiator(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
