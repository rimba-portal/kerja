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

#[Table(name: 'work_flow_instances')]
#[Unguarded]
class WorkFlowInstance extends Model
{
    protected static function booted(): void
    {
        static::creating(fn (self $x) => $x->uuid ??= (string) Str::uuid());
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(WorkFlow::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
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
