<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Work\Enums\LifecycleTransitionType;
use Rimba\Work\Traits\HasHookBindings;

#[Table(name: 'work_flows')]
#[Unguarded]
class Flow extends Model
{
    use HasHookBindings;

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'lifecycle_transition_type' => LifecycleTransitionType::class,
        ];
    }

    public function businessObject(): BelongsTo
    {
        return $this->belongsTo(BusinessObject::class);
    }

    public function lifecyclePhase(): BelongsTo
    {
        return $this->belongsTo(LifecyclePhase::class);
    }

    public function nextLifecyclePhase(): BelongsTo
    {
        return $this->belongsTo(LifecyclePhase::class, 'next_lifecycle_phase_id');
    }

    public function owners(): HasMany
    {
        return $this->hasMany(FlowOwner::class, 'workflow_id');
    }

    public function initiators(): HasMany
    {
        return $this->hasMany(FlowInitiator::class, 'workflow_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(FlowStep::class, 'workflow_id')->orderBy('sequence');
    }

    public function startStep(): ?FlowStep
    {
        return $this->steps()->where('is_start', true)->first();
    }
}
