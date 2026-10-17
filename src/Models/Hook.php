<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Work\Enums\HookOperation;

#[Table(name: 'work_hooks')]
#[Unguarded]
class Hook extends Model
{
    protected function casts(): array
    {
        return [
            'operation' => HookOperation::class,
            'version' => 'integer',
            'schema' => 'array',
            'configuration' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function bindings(): HasMany
    {
        return $this->hasMany(HookBinding::class);
    }
}
