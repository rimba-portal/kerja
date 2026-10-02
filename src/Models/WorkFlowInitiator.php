<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'work_flow_initiators')]
#[Unguarded]
class WorkFlowInitiator extends Model
{
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(WorkFlow::class);
    }
}
