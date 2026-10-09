<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'work_flows')]
#[Unguarded]
class WorkFlow extends Model
{
    public function businessObject(): BelongsTo
    {
        return $this->belongsTo(BusinessObject::class);
    }

    public function lifecyclePhase(): BelongsTo
    {
        return $this->belongsTo(LifecyclePhase::class);
    }

    public function owners(): HasMany
    {
        return $this->hasMany(WorkflowOwner::class, 'workflow_id');
    }

    public function initiators(): HasMany
    {
        return $this->hasMany(WorkFlowInitiator::class, 'workflow_id');
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(WorkFlowFormField::class, 'workflow_id')->orderBy('sequence');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WorkFlowStep::class, 'workflow_id')->orderBy('sequence');
    }

    public function startStep(): ?WorkFlowStep
    {
        return $this->steps()->where('is_start', true)->first();
    }
}
