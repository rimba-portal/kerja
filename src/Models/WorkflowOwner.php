<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'work_flow_owners')]
#[Unguarded]
class WorkflowOwner extends Model
{
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(WorkFlow::class);
    }

    public function jobRole(): BelongsTo
    {
        return $this->belongsTo(config('bites.kerja.models.job_role'));
    }
}
