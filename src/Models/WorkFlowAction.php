<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'work_flow_actions')]
class WorkFlowAction extends Model
{
    protected $guarded = [];

    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkFlowStep::class);
    }

    public function targetWorkFlowStep(): BelongsTo
    {
        return $this->belongsTo(WorkFlowStep::class, 'target_workflow_step_id');
    }
}
