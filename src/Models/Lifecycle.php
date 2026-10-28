<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Work\Enums\LifecycleCategory;

#[Fillable([
    'code',
    'name',
    'description',
    'category',
    'parent_code',
    'sort',
    'is_active',
    'settings',
])]
#[RouteKey('code')]
#[Table(name: 'work_lifecycles')]
class Lifecycle extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'category' => LifecycleCategory::class,
            'sort' => 'integer',
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            Lifecycle::class,
            'parent_code',
            'code',
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            Lifecycle::class,
            'parent_code',
            'code',
        )->orderBy('sort');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(
            LifecyclePhase::class,
            'lifecycle_code',
            'code',
        )->orderBy('sequence');
    }

    public function workflows(): HasMany
    {
        return $this->hasMany(
            Workflow::class,
            'lifecycle_code',
            'code',
        );
    }

    #[Scope]
    protected function active(
        Builder $query,
    ): Builder {
        return $query->where(
            'is_active',
            true,
        );
    }

    #[Scope]
    protected function roots(
        Builder $query,
    ): Builder {
        return $query->whereNull(
            'parent_code',
        );
    }

    #[Scope]
    protected function ordered(
        Builder $query,
    ): Builder {
        return $query
            ->orderBy('sort')
            ->orderBy('name');
    }

    public function isRoot(): bool
    {
        return blank($this->parent_code);
    }

    public function hasParent(): bool
    {
        return filled($this->parent_code);
    }
}
