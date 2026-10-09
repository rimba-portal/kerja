<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\BusinessObject;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Models\LifecyclePhase;
use Rimba\Work\Models\LifecycleTracking;
use Rimba\Work\Models\WorkFlowInstance;

final class LifecycleService
{
    public function track(Model $subject, BusinessObject $object, Lifecycle $lifecycle, LifecyclePhase $phase): LifecycleTracking
    {
        return LifecycleTracking::query()->updateOrCreate(
            ['subject_type' => $subject->getMorphClass(), 'subject_id' => $subject->getKey(), 'lifecycle_id' => $lifecycle->id],
            ['business_object_id' => $object->id, 'lifecycle_phase_id' => $phase->id]
        );
    }

    public function transition(WorkFlowInstance $instance): ?LifecycleTracking
    {
        $workflow = $instance->workflow;
        $phase = $workflow->lifecyclePhase;
        if (! $instance->subject || ! $phase) {
            return null;
        }

        $tracking = LifecycleTracking::query()->whereMorphedTo('subject', $instance->subject)
            ->where('lifecycle_id', $phase->lifecycle_id)->first();
        if (! $tracking) {
            return null;
        }

        $next = $phase->lifecycle->phases()->where('sequence', '>', $phase->sequence)->orderBy('sequence')->first();
        if (! $next) {
            return $tracking;
        }

        $tracking->update(['lifecycle_phase_id' => $next->id, 'last_workflow_instance_id' => $instance->id]);

        return $tracking->fresh();
    }
}
