<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Import;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Rimba\Work\Exceptions\CatalogReferenceNotFound;

final class FlowImporter
{
    public function __construct(
        private ReferenceResolver $referenceResolver,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function import(array $items): int
    {
        foreach ($items as $item) {
            $code = $this->requiredCode($item, 'flow');

            DB::table('work_flows')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $item['name']
                        ?? $this->titleFromCode($code),
                    'description' => $item['description'] ?? null,
                    'owning_org_team_id' => isset($item['owning_org_team'])
                        ? $this->referenceResolver->externalId(
                            'org_team',
                            (string) $item['owning_org_team'],
                        )
                        : null,
                    'business_object_id' => isset($item['business_object'])
                        ? $this->referenceResolver->id(
                            'work_business_objects',
                            (string) $item['business_object'],
                            'business object',
                        )
                        : null,
                    'lifecycle_phase_id' => isset($item['lifecycle_phase'])
                        ? $this->referenceResolver->id(
                            'work_lifecycle_phases',
                            (string) $item['lifecycle_phase'],
                            'lifecycle phase',
                        )
                        : null,
                    'version' => $item['version'] ?? 1,
                    'status' => $item['status'] ?? 'draft',
                    'lifecycle_transition_type' => $item['lifecycle_transition_type'] ?? 'stay',
                    'next_lifecycle_phase_id' => isset($item['next_lifecycle_phase'])
                            ? $this->referenceResolver->id(
                                'work_lifecycle_phases',
                                (string) $item['next_lifecycle_phase'],
                                'lifecycle phase',
                            )
                            : null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );

            $flowId = $this->referenceResolver->flowId($code);

            $this->syncRoles($flowId, $item);
            $this->syncSteps($flowId, $item['steps'] ?? []);
        }

        return count($items);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function syncRoles(
        int $flowId,
        array $item,
    ): void {
        DB::table('work_flow_owners')
            ->where('workflow_id', $flowId)
            ->delete();

        foreach ($item['owner_roles'] ?? [] as $roleValue) {
            $roleCode = is_array($roleValue)
                ? ($roleValue['job_role'] ?? $roleValue['code'] ?? null)
                : $roleValue;

            if (! is_string($roleCode) || blank($roleCode)) {
                throw new InvalidArgumentException(
                    'Flow owner role requires a JobRole code.'
                );
            }

            DB::table('work_flow_owners')->insert([
                'workflow_id' => $flowId,
                'job_role_id' => $this->referenceResolver->externalId(
                    'job_role',
                    $roleCode,
                ),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('work_flow_initiators')
            ->where('workflow_id', $flowId)
            ->delete();

        foreach ($item['initiator_roles'] ?? [] as $roleValue) {
            $definition = is_array($roleValue)
                ? $roleValue
                : ['job_role' => $roleValue];

            $roleCode = $definition['job_role']
                ?? $definition['code']
                ?? null;

            if (! is_string($roleCode) || blank($roleCode)) {
                throw new InvalidArgumentException(
                    'Flow initiator requires a JobRole code.'
                );
            }

            DB::table('work_flow_initiators')->insert([
                'workflow_id' => $flowId,
                'org_team_id' => isset($definition['org_team'])
                    ? $this->referenceResolver->externalId(
                        'org_team',
                        (string) $definition['org_team'],
                    )
                    : null,
                'job_role_id' => $this->referenceResolver->externalId(
                    'job_role',
                    $roleCode,
                ),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     */
    private function syncSteps(
        int $flowId,
        array $steps,
    ): void {
        $stepIds = [];

        /*
         * First pass creates every Step so Actions may reference Steps
         * declared later in the JSON document.
         */
        foreach ($steps as $index => $step) {
            $code = $this->requiredCode($step, 'flow step');

            DB::table('work_flow_steps')->updateOrInsert(
                [
                    'workflow_id' => $flowId,
                    'code' => $code,
                ],
                [
                    'work_package_id' => $this->referenceResolver->id(
                        'work_packages',
                        (string) $step['work_package'],
                        'package',
                    ),
                    'name' => $step['name']
                        ?? $this->titleFromCode($code),
                    'step_type' => $step['step_type'],
                    'sequence' => $step['sequence'] ?? $index + 1,
                    'is_start' => $step['is_start'] ?? $index === 0,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );

            $stepIds[$code] = $this->referenceResolver->stepId(
                $flowId,
                $code,
            );
        }

        /*
         * Delete stale Steps that are no longer declared by the catalog.
         * Their Actions cascade-delete through the database relationship.
         */
        if ($stepIds !== []) {
            DB::table('work_flow_steps')
                ->where('workflow_id', $flowId)
                ->whereNotIn('id', array_values($stepIds))
                ->delete();
        }

        foreach ($steps as $step) {
            $stepCode = (string) $step['code'];
            $stepId = $stepIds[$stepCode];

            DB::table('work_flow_actions')
                ->where('workflow_step_id', $stepId)
                ->delete();

            foreach ($step['actions'] ?? [] as $index => $action) {
                $targetStepId = null;

                if (filled($action['target_step'] ?? null)) {
                    $targetCode = (string) $action['target_step'];

                    $targetStepId = $stepIds[$targetCode]
                        ?? throw CatalogReferenceNotFound::for(
                            'flow step',
                            $targetCode,
                        );
                }

                DB::table('work_flow_actions')->insert([
                    'workflow_step_id' => $stepId,
                    'action_type' => $action['action_type'],
                    'target_workflow_step_id' => $targetStepId,
                    'terminal_status' => $action['terminal_status'] ?? null,
                    'requires_confirmation' => $action['requires_confirmation'] ?? false,
                    'sequence' => $action['sequence'] ?? $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function requiredCode(
        array $item,
        string $type,
    ): string {
        $code = $item['code'] ?? null;

        if (! is_string($code) || blank($code)) {
            throw new InvalidArgumentException(
                ucfirst($type).' requires a non-empty [code].'
            );
        }

        return $code;
    }

    private function titleFromCode(string $code): string
    {
        return Str::of($code)
            ->replace(['_', '-'], ' ')
            ->lower()
            ->title()
            ->toString();
    }
}
