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
use Rimba\Organization\Models\OrgUnit;
use Rimba\Work\Enums\WorkflowProviderType;

#[Fillable([
    'org_unit_id',
    'code',
    'name',
    'description',
    'lifecycle_code',
    'lifecycle_phase_code',
    'provider',
    'provider_key',
    'provider_reference',
    'version',
    'is_active',
    'settings',
])]
#[RouteKey('code')]
#[Table(name: 'work_workflows')]
class Workflow extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'provider' => WorkflowProviderType::class,

            'version' => 'integer',
            'is_active' => 'boolean',

            'settings' => 'array',
        ];
    }

    public function lifecycle(): BelongsTo
    {
        return $this->belongsTo(
            Lifecycle::class,
            'lifecycle_code',
            'code',
        );
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(
            OrgUnit::class,
            'org_unit_id',
        );
    }

    /*
     * LifecyclePhase has a composite logical identity:
     *
     * lifecycle_code + code
     *
     * A normal BelongsTo cannot enforce both columns, so use phase()
     * for convenient loading and phaseQuery() where strict qualification
     * is required.
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(
            LifecyclePhase::class,
            'lifecycle_phase_code',
            'code',
        );
    }

    public function phaseQuery()
    {
        return LifecyclePhase::query()
            ->where(
                'lifecycle_code',
                $this->lifecycle_code,
            )
            ->where(
                'code',
                $this->lifecycle_phase_code,
            );
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(
            Offering::class,
            'workflow_code',
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
    protected function forLifecycle(
        Builder $query,
        Lifecycle|string $lifecycle,
    ): Builder {
        return $query->where(
            'lifecycle_code',
            $lifecycle instanceof Lifecycle
                ? $lifecycle->code
                : $lifecycle,
        );
    }

    public function isPackstub(): bool
    {
        return $this->provider
            === WorkflowProviderType::Packstub;
    }

    public function hasProviderDefinition(): bool
    {
        return filled($this->provider_key);
    }
}
