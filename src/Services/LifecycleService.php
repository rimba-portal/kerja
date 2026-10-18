<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\LifecycleTransitionType;
use Rimba\Work\Models\BusinessObject;
use Rimba\Work\Models\FlowInstance;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Models\LifecyclePhase;
use Rimba\Work\Models\LifecycleTracking;
use RuntimeException;

final class LifecycleService
{
    public function track(
        Model $subject,
        BusinessObject $businessObject,
        Lifecycle $lifecycle,
        LifecyclePhase $phase,
    ): LifecycleTracking {
        return LifecycleTracking::query()->updateOrCreate(
            [
                'subject_type' => $subject->getMorphClass(),
                'subject_id' => $subject->getKey(),
                'lifecycle_id' => $lifecycle->id,
            ],
            [
                'business_object_id' => $businessObject->id,
                'lifecycle_phase_id' => $phase->id,
            ],
        );
    }

    public function ensureTracking(
        FlowInstance $instance,
    ): ?LifecycleTracking {
        $flow = $instance->flow;
        $phase = $flow->lifecyclePhase;
        $subject = $instance->subject;

        if (
            ! $subject
            || ! $phase
            || ! $flow->businessObject
        ) {
            return null;
        }

        return LifecycleTracking::query()->firstOrCreate(
            [
                'subject_type' => $subject->getMorphClass(),
                'subject_id' => $subject->getKey(),
                'lifecycle_id' => $phase->lifecycle_id,
            ],
            [
                'business_object_id' => $flow->business_object_id,
                'lifecycle_phase_id' => $phase->id,
                'last_workflow_instance_id' => $instance->id,
            ],
        );
    }

    public function transition(
        FlowInstance $instance,
    ): ?LifecycleTracking {
        $flow = $instance->flow;
        $phase = $flow->lifecyclePhase;

        if (! $instance->subject || ! $phase) {
            return null;
        }

        $tracking = LifecycleTracking::query()
            ->whereMorphedTo('subject', $instance->subject)
            ->where('lifecycle_id', $phase->lifecycle_id)
            ->first();

        $tracking ??= $this->ensureTracking($instance);

        if (! $tracking) {
            return null;
        }

        $type = $flow->lifecycle_transition_type
            ?? LifecycleTransitionType::Stay;

        if ($type === LifecycleTransitionType::Stay) {
            $tracking->update([
                'last_workflow_instance_id' => $instance->id,
            ]);

            return $tracking->fresh();
        }

        if ($type === LifecycleTransitionType::Transition) {
            $target = $flow->nextLifecyclePhase
                ?? throw new RuntimeException(
                    'Explicit Lifecycle transition requires a target phase.'
                );

            if ($target->lifecycle_id !== $phase->lifecycle_id) {
                throw new RuntimeException(
                    'Target LifecyclePhase must belong to the same Lifecycle.'
                );
            }
        } else {
            $target = $phase->lifecycle
                ->phases()
                ->where('sequence', '>', $phase->sequence)
                ->orderBy('sequence')
                ->first();

            if (! $target) {
                $tracking->update([
                    'last_workflow_instance_id' => $instance->id,
                ]);

                return $tracking->fresh();
            }
        }

        $tracking->update([
            'lifecycle_phase_id' => $target->id,
            'last_workflow_instance_id' => $instance->id,
        ]);

        return $tracking->fresh();
    }
}
