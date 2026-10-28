<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'lifecycle_code',
    'code',
    'name',
    'description',
    'sequence',
    'entry_criteria',
    'exit_criteria',
    'settings',
])]
#[Table(name: 'work_lifecycle_phases')]
class LifecyclePhase extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'entry_criteria' => 'array',
            'exit_criteria' => 'array',
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

    public function workflowEntries()
    {
        return Workflow::query()
            ->where(
                'lifecycle_code',
                $this->lifecycle_code,
            )
            ->where(
                'lifecycle_phase_code',
                $this->code,
            );
    }

    public function qualifiedCode(): string
    {
        return "{$this->lifecycle_code}:{$this->code}";
    }
}
