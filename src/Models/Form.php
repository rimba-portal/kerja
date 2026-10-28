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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Work\Enums\FormDisplayMode;
use Rimba\Work\Enums\FormProviderType;

#[Fillable([
    'code',
    'name',
    'description',
    'provider',
    'provider_key',
    'display_mode',
    'schema',
    'settings',
    'version',
    'is_active',
])]
#[RouteKey('code')]
#[Table(name: 'work_forms')]
class Form extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'provider' => FormProviderType::class,
            'display_mode' => FormDisplayMode::class,

            'schema' => 'array',
            'settings' => 'array',

            'version' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(
            Offering::class,
            'form_code',
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
    protected function provider(
        Builder $query,
        FormProviderType|string $provider,
    ): Builder {
        return $query->where(
            'provider',
            $provider instanceof FormProviderType
                ? $provider->value
                : $provider,
        );
    }

    public function isPackstub(): bool
    {
        return $this->provider
            === FormProviderType::Packstub;
    }

    public function isLocal(): bool
    {
        return $this->provider
            === FormProviderType::Local;
    }

    public function hasProviderDefinition(): bool
    {
        return filled($this->provider_key);
    }
}
