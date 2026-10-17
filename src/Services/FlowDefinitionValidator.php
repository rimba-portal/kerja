<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use InvalidArgumentException;
use Rimba\Work\Enums\ActionType;
use Rimba\Work\Enums\FlowInstanceStatus;
use Rimba\Work\Enums\HookOperation;
use Rimba\Work\Enums\HookPoint;
use Rimba\Work\Enums\LifecycleTransitionType;
use Rimba\Work\Models\Endpoint;
use Rimba\Work\Models\Flow;
use Rimba\Work\Models\Hook;

final class FlowDefinitionValidator
{
    public function validate(Flow $workflow): void
    {
        $workflow->load([
            'steps.package',
            'steps.actions.targetFlowStep',
            'hookBindings.hook',
            'steps.hookBindings.hook',
            'steps.actions.hookBindings.hook',
            'lifecyclePhase',
            'nextLifecyclePhase',
        ]);

        if ($workflow->steps->isEmpty()) {
            throw new InvalidArgumentException(
                "Workflow [{$workflow->code}] must contain at least one Step."
            );
        }

        if ($workflow->steps->where('is_start', true)->count() !== 1) {
            throw new InvalidArgumentException(
                "Workflow [{$workflow->code}] must contain exactly one start Step."
            );
        }

        foreach ($workflow->steps as $step) {
            if (! $step->package) {
                throw new InvalidArgumentException(
                    "Step [{$step->code}] requires a Package."
                );
            }

            if ($step->actions->isEmpty()) {
                throw new InvalidArgumentException(
                    "Step [{$step->code}] requires at least one FlowAction."
                );
            }

            foreach ($step->actions as $action) {
                if ($action->action_type === ActionType::Withdraw) {
                    throw new InvalidArgumentException(
                        'Withdraw is a FlowInstance action, not a Step action.'
                    );
                }

                if (! $step->step_type->allows($action->action_type)) {
                    throw new InvalidArgumentException(
                        "ActionType [{$action->action_type->value}] "
                        ."is invalid for StepType [{$step->step_type->value}]."
                    );
                }

                $hasTarget =
                    $action->target_workflow_step_id !== null;

                $hasTerminal =
                    $action->terminal_status !== null;

                if ($hasTarget === $hasTerminal) {
                    throw new InvalidArgumentException(
                        'FlowAction must define exactly one target or terminal status.'
                    );
                }

                if (
                    $hasTarget
                    && $action->targetFlowStep?->workflow_id
                        !== $workflow->id
                ) {
                    throw new InvalidArgumentException(
                        'FlowAction target must belong to the same Workflow.'
                    );
                }

                if (
                    $action->terminal_status
                    === FlowInstanceStatus::Active
                ) {
                    throw new InvalidArgumentException(
                        'Active is not a terminal status.'
                    );
                }

                foreach ($action->hookBindings as $binding) {
                    $this->assertPoint(
                        $binding->hook_point,
                        HookPoint::ActionExit,
                    );

                    $this->validateHook($binding->hook);
                }
            }

            foreach ($step->hookBindings as $binding) {
                if (! in_array(
                    $binding->hook_point,
                    [HookPoint::StepEnter, HookPoint::StepExit],
                    true,
                )) {
                    throw new InvalidArgumentException(
                        'FlowStep HookBinding must be step_enter or step_exit.'
                    );
                }

                $this->validateHook($binding->hook);
            }
        }

        foreach ($workflow->hookBindings as $binding) {
            $this->assertPoint(
                $binding->hook_point,
                HookPoint::WorkflowLoad,
            );

            $this->validateHook($binding->hook);
        }

        $type = $workflow->lifecycle_transition_type
            ?? LifecycleTransitionType::Stay;

        if ($type === LifecycleTransitionType::Transition) {
            if (
                ! $workflow->lifecyclePhase
                || ! $workflow->nextLifecyclePhase
            ) {
                throw new InvalidArgumentException(
                    'Explicit Lifecycle transition requires source and target phases.'
                );
            }

            if (
                $workflow->lifecyclePhase->lifecycle_id
                !== $workflow->nextLifecyclePhase->lifecycle_id
            ) {
                throw new InvalidArgumentException(
                    'Lifecycle transition target must belong to the same Lifecycle.'
                );
            }
        } elseif ($workflow->next_lifecycle_phase_id !== null) {
            throw new InvalidArgumentException(
                'next_lifecycle_phase_id is only valid for transition type.'
            );
        }
    }

    private function assertPoint(
        HookPoint $actual,
        HookPoint $expected,
    ): void {
        if ($actual !== $expected) {
            throw new InvalidArgumentException(
                "HookBinding point must be [{$expected->value}]."
            );
        }
    }

    private function validateHook(Hook $hook): void
    {
        if (! $hook->is_active) {
            throw new InvalidArgumentException(
                "Hook [{$hook->code}] is inactive."
            );
        }

        $configuration = $hook->configuration ?? [];

        if (! is_array($configuration)) {
            throw new InvalidArgumentException(
                "Hook [{$hook->code}] configuration must be an array."
            );
        }

        if (in_array(
            $hook->operation,
            [HookOperation::Get, HookOperation::Put],
            true,
        )) {
            $endpoint = $configuration['endpoint'] ?? null;

            if (! is_string($endpoint) || blank($endpoint)) {
                throw new InvalidArgumentException(
                    "Hook [{$hook->code}] requires an Endpoint."
                );
            }

            if (! Endpoint::query()
                ->where('code', $endpoint)
                ->where('is_active', true)
                ->exists()) {
                throw new InvalidArgumentException(
                    "Hook [{$hook->code}] references unavailable Endpoint [{$endpoint}]."
                );
            }

            if (blank($configuration['path'] ?? null)) {
                throw new InvalidArgumentException(
                    "Hook [{$hook->code}] requires a request path."
                );
            }
        }

        foreach ($configuration['mappings'] ?? [] as $mapping) {
            if (! is_array($mapping)) {
                throw new InvalidArgumentException(
                    "Hook [{$hook->code}] contains an invalid mapping."
                );
            }

            if (! isset($mapping['from'], $mapping['to'])) {
                throw new InvalidArgumentException(
                    "Hook [{$hook->code}] mapping requires from and to."
                );
            }
        }
    }
}
