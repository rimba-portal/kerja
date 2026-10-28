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
use Rimba\Work\Enums\OfferingAvailability;
use Rimba\Work\Enums\OfferingVisibility;
use Rimba\Work\Enums\SubjectCreationMode;

#[Fillable([
    'code',
    'name',
    'description',

    'org_team_id',

    'form_code',
    'workflow_code',

    'subject_type',
    'subject_creation_mode',

    'input_mapping',
    'context_mapping',
    'defaults',

    'visibility',
    'availability',

    'icon',
    'color',
    'sort',

    'is_active',
    'settings',
])]
#[RouteKey('code')]
#[Table(name: 'work_offerings')]
class Offering extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'subject_creation_mode' => SubjectCreationMode::class,

            'visibility' => OfferingVisibility::class,

            'availability' => OfferingAvailability::class,

            'input_mapping' => 'array',
            'context_mapping' => 'array',
            'defaults' => 'array',
            'settings' => 'array',

            'sort' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(
            Form::class,
            'form_code',
            'code',
        );
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(
            Workflow::class,
            'workflow_code',
            'code',
        );
    }

    public function executions(): HasMany
    {
        return $this->hasMany(
            OfferingExecution::class,
            'offering_code',
            'code',
        );
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(
            config('work.models.org_team'),
            'org_team_id',
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
    protected function available(
        Builder $query,
    ): Builder {
        return $query
            ->active()
            ->where(
                'availability',
                OfferingAvailability::Available->value,
            );
    }

    #[Scope]
    protected function forTeam(
        Builder $query,
        string $teamId,
    ): Builder {
        return $query->where(
            'org_team_id',
            $teamId,
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

    public function requiresForm(): bool
    {
        return filled($this->form_code);
    }

    public function requiresWorkflow(): bool
    {
        return filled($this->workflow_code);
    }

    public function requiresSubject(): bool
    {
        return filled($this->subject_type)
            && $this->subject_creation_mode
                !== SubjectCreationMode::None;
    }

    public function canCreateSubject(): bool
    {
        return $this->subject_creation_mode
            === SubjectCreationMode::Create;
    }

    public function canSelectSubject(): bool
    {
        return $this->subject_creation_mode
            === SubjectCreationMode::Select;
    }

    public function canUpdateSubject(): bool
    {
        return $this->subject_creation_mode
            === SubjectCreationMode::Update;
    }
}
