<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'work_workflows')]
class WorkFlow extends Model
{
    protected $guarded = [];

    public function initiators(): HasMany
    {
        return $this->hasMany(WorkFlowInitiator::class);
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(WorkFlowFormField::class)->orderBy('sequence');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(WorkFlowStep::class)->orderBy('sequence');
    }

    public function startStep(): ?WorkFlowStep
    {
        return $this->steps()->where('is_start', true)->first();
    }
}
