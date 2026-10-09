<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Table(name: 'work_lifecycle_trackings')]
#[Unguarded]
class LifecycleTracking extends Model
{
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function businessObject(): BelongsTo
    {
        return $this->belongsTo(BusinessObject::class);
    }

    public function lifecycle(): BelongsTo
    {
        return $this->belongsTo(Lifecycle::class);
    }

    public function lifecyclePhase(): BelongsTo
    {
        return $this->belongsTo(LifecyclePhase::class);
    }

    public function lastWorkflowInstance(): BelongsTo
    {
        return $this->belongsTo(WorkFlowInstance::class, 'last_workflow_instance_id');
    }
}
