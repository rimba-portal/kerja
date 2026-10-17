<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rimba\Work\Enums\HookPoint;

#[Table(name: 'work_hook_bindings')]
#[Unguarded]
class HookBinding extends Model
{
    protected function casts(): array
    {
        return [
            'hook_point' => HookPoint::class,
            'sequence' => 'integer',
            'parameters' => 'array',
            'is_required' => 'boolean',
            'is_enabled' => 'boolean',
        ];
    }

    public function hook(): BelongsTo
    {
        return $this->belongsTo(Hook::class);
    }

    public function hookable(): MorphTo
    {
        return $this->morphTo();
    }
}
