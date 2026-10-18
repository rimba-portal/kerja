<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'work_flow_initiators')]
#[Unguarded]
class FlowInitiator extends Model
{
    public function flow(): BelongsTo
    {
        return $this->belongsTo(
            Flow::class,
            'workflow_id',
        );
    }

    public function orgTeam(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.kerja.models.org_team'),
            'org_team_id',
        );
    }

    public function jobRole(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.kerja.models.job_role'),
            'job_role_id',
        );
    }
}
