<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'work_lifecycle_phases')]
#[Unguarded]
class LifecyclePhase extends Model
{
    public function lifecycle(): BelongsTo
    {
        return $this->belongsTo(Lifecycle::class);
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(WorkFlow::class);
    }
}
