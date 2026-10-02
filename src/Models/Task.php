<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

#[Table(name: 'work_tasks')]
#[Unguarded]
class Task extends Model
{
    protected static function booted(): void
    {
        static::creating(fn (self $x) => $x->uuid ??= (string) Str::uuid());
    }

    public function workflowInstance(): BelongsTo
    {
        return $this->belongsTo(WorkFlowInstance::class);
    }

    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkFlowStep::class);
    }

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class);
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo();
    }
}
