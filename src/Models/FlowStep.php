<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Work\Enums\StepType;
use Rimba\Work\Traits\HasHookBindings;

#[Table(name: 'work_flow_steps')]
#[Unguarded]
class FlowStep extends Model
{
    use HasHookBindings;

    protected function casts(): array
    {
        return [
            'step_type' => StepType::class,
            'sequence' => 'integer',
            'is_start' => 'boolean',
        ];
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(
            Flow::class,
            'workflow_id',
        );
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(
            Package::class,
            'work_package_id',
        );
    }

    public function actions(): HasMany
    {
        return $this->hasMany(
            FlowAction::class,
            'workflow_step_id',
        )->orderBy('sequence');
    }
}
