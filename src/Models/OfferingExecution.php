<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rimba\Organization\Models\OrgTeam;
use Rimba\Organization\Models\OrgUnit;
use Rimba\People\Models\Staff;
use Rimba\Work\Enums\ExecutionStatus;

#[Fillable([
    'offering_code',

    'user_id',
    'staff_id',
    'org_unit_id',
    'org_team_id',

    'subject_type',
    'subject_id',

    'workflow_instance_type',
    'workflow_instance_id',

    'form_submission_type',
    'form_submission_id',

    'status',

    'input_snapshot',
    'context_snapshot',
    'mapped_snapshot',
    'result_snapshot',

    'failure_code',
    'failure_message',

    'started_at',
    'completed_at',
    'failed_at',
    'cancelled_at',
])]
#[Table(name: 'work_offering_executions')]
class OfferingExecution extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'status' => ExecutionStatus::class,

            'input_snapshot' => 'array',
            'context_snapshot' => 'array',
            'mapped_snapshot' => 'array',
            'result_snapshot' => 'array',

            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(
            Offering::class,
            'offering_code',
            'code',
        );
    }

    public function subject(): MorphTo
    {
        return $this->morphTo(
            __FUNCTION__,
            'subject_type',
            'subject_id',
        );
    }

    public function workflowInstance(): MorphTo
    {
        return $this->morphTo(
            __FUNCTION__,
            'workflow_instance_type',
            'workflow_instance_id',
        );
    }

    public function formSubmission(): MorphTo
    {
        return $this->morphTo(
            __FUNCTION__,
            'form_submission_type',
            'form_submission_id',
        );
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(
            Staff::class,
            'staff_id',
        );
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(
            OrgUnit::class,
            'org_unit_id',
        );
    }

    public function orgTeam(): BelongsTo
    {
        return $this->belongsTo(
            OrgTeam::class,
            'org_team_id',
        );
    }

    #[Scope]
    protected function pending(
        Builder $query,
    ): Builder {
        return $query->where(
            'status',
            ExecutionStatus::Pending->value,
        );
    }

    #[Scope]
    protected function running(
        Builder $query,
    ): Builder {
        return $query->where(
            'status',
            ExecutionStatus::Running->value,
        );
    }

    #[Scope]
    protected function completed(
        Builder $query,
    ): Builder {
        return $query->where(
            'status',
            ExecutionStatus::Completed->value,
        );
    }

    #[Scope]
    protected function failed(
        Builder $query,
    ): Builder {
        return $query->where(
            'status',
            ExecutionStatus::Failed->value,
        );
    }

    public function markRunning(): static
    {
        $this->forceFill([
            'status' => ExecutionStatus::Running,

            'started_at' => $this->started_at
                ?? now(),
        ])->save();

        return $this->refresh();
    }

    public function markCompleted(
        array $result = [],
    ): static {
        $this->forceFill([
            'status' => ExecutionStatus::Completed,

            'result_snapshot' => array_replace_recursive(
                $this->result_snapshot ?? [],
                $result,
            ),

            'completed_at' => now(),

            'failed_at' => null,
            'cancelled_at' => null,
        ])->save();

        return $this->refresh();
    }

    public function markFailed(
        string $message,
        ?string $code = null,
    ): static {
        $this->forceFill([
            'status' => ExecutionStatus::Failed,

            'failure_code' => $code,
            'failure_message' => $message,

            'failed_at' => now(),
        ])->save();

        return $this->refresh();
    }

    public function markCancelled(): static
    {
        $this->forceFill([
            'status' => ExecutionStatus::Cancelled,
            'cancelled_at' => now(),
        ])->save();

        return $this->refresh();
    }

    public function isPending(): bool
    {
        return $this->status
            === ExecutionStatus::Pending;
    }

    public function isRunning(): bool
    {
        return $this->status
            === ExecutionStatus::Running;
    }

    public function isCompleted(): bool
    {
        return $this->status
            === ExecutionStatus::Completed;
    }

    public function isFailed(): bool
    {
        return $this->status
            === ExecutionStatus::Failed;
    }

    public function isTerminal(): bool
    {
        return in_array(
            $this->status,
            [
                ExecutionStatus::Completed,
                ExecutionStatus::Failed,
                ExecutionStatus::Cancelled,
            ],
            true,
        );
    }
}
