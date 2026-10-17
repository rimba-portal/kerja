<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Rimba\Organization\Models\OrgTeam;
use Rimba\Organization\Models\OrgUnit;
use Rimba\Position\Models\JobRole;
use Rimba\Work\Exceptions\CatalogReferenceNotFound;
use Rimba\Work\Models\Flow;
use Rimba\Work\Models\FlowAction;
use Rimba\Work\Models\FlowStep;
use RuntimeException;

final class WorkSeedImportService
{
    public function __construct(
        private FlowDefinitionValidator $workflowDefinitionValidator,
    ) {}

    public function import(string $source): array
    {
        $payload = $this->loadJson($source);

        return DB::transaction(function () use ($payload): array {
            return ['org_units' => $this->importOrgUnits($payload['org_units'] ?? []), 'org_teams' => $this->importOrgTeams($payload['org_teams'] ?? []), 'job_roles' => $this->importJobRoles($payload['job_roles'] ?? []), 'activity_types' => $this->importSimple('work_activity_types', $payload['activity_types'] ?? []), 'business_objects' => $this->importSimple('work_business_objects', $payload['business_objects'] ?? []), 'lifecycles' => $this->importSimple('work_lifecycles', $payload['lifecycles'] ?? []), 'lifecycle_phases' => $this->importSimple('work_lifecycle_phases', $payload['lifecycle_phases'] ?? []), 'work_packages' => $this->importPackages($payload['work_packages'] ?? []), 'work_flows' => $this->importFlows($payload['work_flows'] ?? []), 'endpoints' => $this->importEndpoints($payload['endpoints'] ?? []), 'hooks' => $this->importHooks($payload['hooks'] ?? []), 'hook_bindings' => $this->importHookBindings($payload['hook_bindings'] ?? [])];
        });
    }

    /*
     * =============================================================
     * JSON loading
     * =============================================================
     */

    private function loadJson(string $source): array
    {
        $contents = str_starts_with($source, 'http')
            ? file_get_contents($source)
            : File::get($source);

        if ($contents === false) {
            throw new RuntimeException(
                "Unable to read work catalog [{$source}]."
            );
        }

        $decoded = json_decode(
            (string) $contents,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (! is_array($decoded)) {
            throw new RuntimeException(
                'Invalid JSON structure.'
            );
        }

        return $decoded;
    }

    /*
     * =============================================================
     * Organization bootstrap
     * =============================================================
     */

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importOrgUnits(array $items): int
    {
        /*
         * OrgUnits may refer to parent OrgUnits.
         *
         * First pass ensures that every unit exists.
         * Second pass resolves parent references.
         */

        foreach ($items as $item) {
            $code = $this->requiredCode(
                $item,
                'organizational unit'
            );

            $unit = OrgUnit::query()->where('code', $code)->first();

            if (! $unit) {
                OrgUnit::query()->create(['uuid' => (string) Str::uuid(),    'code' => $code,    'name' => $item['name'] ?? $this->titleFromCode($code),    'description' => $item['description'] ?? null,    'attributes' => $item['attributes'] ?? null,    /*      * org_corp_id may remain null according      * to the current Organization schema. */ 'org_corp_id' => null,    /*      * Parent is resolved during the second pass. */ 'parent_id' => null]);

                continue;
            }

            /*
             * Kerja does not own Organization master data.
             *
             * Existing units are retained. We only fill attributes
             * that are absent rather than overwriting organization-
             * managed information.
             */

            $changes = [];

            if (blank($unit->name)) {
                $changes['name'] = $item['name'] ?? $this->titleFromCode($code);
            }

            if (
                blank($unit->description) && array_key_exists('description', $item)
            ) {
                $changes['description'] = $item['description'];
            }

            if (
                empty($unit->attributes) && array_key_exists('attributes', $item)
            ) {
                $changes['attributes'] = $item['attributes'];
            }

            if ($changes !== []) {
                $unit->update($changes);
            }
        }

        /*
         * Resolve optional parent relationships only after every
         * OrgUnit from the catalog has been created.
         */

        foreach ($items as $item) {
            if (blank($item['parent'] ?? null)) {
                continue;
            }

            $code = $this->requiredCode(
                $item,
                'organizational unit'
            );

            $unit = OrgUnit::query()->where('code', $code)->firstOrFail();

            /*
             * Preserve existing Organization-owned hierarchy.
             * Only populate parent when no parent exists yet.
             */

            if ($unit->parent_id !== null) {
                continue;
            }

            $unit->update([
                'parent_id' => $this->modelId(OrgUnit::class, (string) $item['parent'], 'organizational unit'),
            ]);
        }

        return count($items);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importOrgTeams(array $items): int
    {
        foreach ($items as $item) {
            $code = $this->requiredCode(
                $item,
                'organizational team'
            );

            $orgUnitCode = $item['org_unit'] ?? null;

            if (blank($orgUnitCode)) {
                throw new InvalidArgumentException("OrgTeam [{$code}] requires [org_unit].");
            }

            $orgUnitId = $this->modelId(
                OrgUnit::class,
                (string) $orgUnitCode,
                'organizational unit'
            );

            $team = OrgTeam::query()->where('code', $code)->first();

            if (! $team) {
                OrgTeam::query()->create(['code' => $code,    'name' => $item['name'] ?? $this->titleFromCode($code),    'org_unit_id' => $orgUnitId,    'is_active' => $item['is_active'] ?? true,    'attributes' => $item['attributes'] ?? null]);

                continue;
            }

            /*
             * Preserve existing Organization-owned values.
             *
             * In particular, do not silently move an existing team
             * to another OrgUnit merely because a Kerja seed is rerun.
             */

            $changes = [];

            if (blank($team->name)) {
                $changes['name'] = $item['name'] ?? $this->titleFromCode($code);
            }

            if (
                empty($team->attributes) && array_key_exists('attributes', $item)
            ) {
                $changes['attributes'] = $item['attributes'];
            }

            if ($changes !== []) {
                $team->update($changes);
            }
        }

        return count($items);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importJobRoles(array $items): int
    {
        foreach ($items as $item) {
            $code = $this->requiredCode(
                $item,
                'job role'
            );

            $role = JobRole::query()->where('code', $code)->first();

            if (! $role) {
                JobRole::query()->create(['code' => $code,    'name' => $item['name'] ?? $this->titleFromCode($code),    'type' => $item['type'] ?? null,    'description' => $item['description'] ?? null,    'attributes' => $item['attributes'] ?? null]);

                continue;
            }

            /*
             * JobRole belongs to the Position package.
             * Kerja seeds may bootstrap missing roles but should not
             * overwrite established Position master data.
             */

            $changes = [];

            if (blank($role->name)) {
                $changes['name'] = $item['name'] ?? $this->titleFromCode($code);
            }

            if (
                blank($role->type) && array_key_exists('type', $item)
            ) {
                $changes['type'] = $item['type'];
            }

            if (
                blank($role->description) && array_key_exists('description', $item)
            ) {
                $changes['description'] = $item['description'];
            }

            if (
                empty($role->attributes) && array_key_exists('attributes', $item)
            ) {
                $changes['attributes'] = $item['attributes'];
            }

            if ($changes !== []) {
                $role->update($changes);
            }
        }

        return count($items);
    }

    /*
     * =============================================================
     * Simple Kerja catalogs
     * =============================================================
     */

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importSimple(
        string $table,
        array $items
    ): int {
        foreach ($items as $item) {
            $code = $this->requiredCode(
                $item,
                $table
            );

            $row = $item;

            unset($row['code']);

            /*
             * Resolve supported code references.
             */

            foreach (['business_object' => 'business_object_id',     'lifecycle' => 'lifecycle_id'] as $key => $foreignKey) {
                if (! isset($row[$key])) {
                    continue;
                }

                $tableName = $key === 'lifecycle' ? 'work_lifecycles' : 'work_business_objects';
                $row[$foreignKey] = $this->id($tableName, (string) $row[$key], str_replace('_', ' ', $key));
                unset($row[$key]);
            }

            DB::table($table)->updateOrInsert(
                ['code' => $code],
                $row + ['updated_at' => now(),     'created_at' => now()]
            );
        }

        return count($items);
    }

    /*
     * =============================================================
     * Packages
     * =============================================================
     */

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importPackages(
        array $items
    ): int {
        foreach ($items as $item) {
            $code = $this->requiredCode(
                $item,
                'work package'
            );

            $responsibleJobRole = $item['responsible_job_role'] ?? $item['actor_job_role'] ?? null;

            if (blank($responsibleJobRole)) {
                throw new InvalidArgumentException("Package [{$code}] requires ".'[responsible_job_role].');
            }

            if (blank($item['org_team'] ?? null)) {
                throw new InvalidArgumentException("Package [{$code}] requires [org_team].");
            }

            DB::table('work_packages')->updateOrInsert(
                ['code' => $code],
                ['name' => $item['name'] ?? $this->titleFromCode($code),    'description' => $item['description'] ?? null,    'org_team_id' => $this->externalId('org_team', (string) $item['org_team']),    'actor_job_role_id' => $this->externalId('job_role', (string) $responsibleJobRole),    'activity_type_id' => $this->id('work_activity_types', (string) $item['activity_type'], 'activity type'),    'business_object_id' => $this->id('work_business_objects', (string) $item['business_object'], 'business object'),    'version' => $item['version'] ?? 1,    'status' => $item['status'] ?? 'draft',    'updated_at' => now(),    'created_at' => now()]
            );

            $packageId = $this->id(
                'work_packages',
                $code,
                'work package'
            );

            $this->syncPackageParties(
                $packageId,
                $item
            );

            $this->syncPackagePayloads(
                $packageId,
                $item
            );
        }

        return count($items);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function syncPackageParties(
        int $packageId,
        array $item
    ): void {
        DB::table('work_package_parties')
            ->where(
                'work_package_id',
                $packageId
            )
            ->delete();

        foreach (
            [
                'suppliers' => 'supplier',
                'customers' => 'customer',
            ] as $key => $side
        ) {
            foreach ($item[$key] ?? [] as $index => $value) {
                DB::table('work_package_parties')->insert(['work_package_id' => $packageId,    'side' => $side,    'name' => is_array($value) ? $value['name'] : $value,    'sequence' => $index + 1,    'created_at' => now(),    'updated_at' => now()]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function syncPackagePayloads(
        int $packageId,
        array $item
    ): void {
        DB::table('work_package_payloads')
            ->where(
                'work_package_id',
                $packageId
            )
            ->delete();

        foreach (
            [
                'inputs' => 'input',
                'outputs' => 'output',
            ] as $key => $direction
        ) {
            foreach ($item[$key] ?? [] as $index => $value) {
                $payload = is_array($value) ? $value : ['code' => str($value)->snake()->upper()->value(),    'name' => $value];
                DB::table('work_package_payloads')->insert(['work_package_id' => $packageId,    'direction' => $direction,    'code' => $payload['code'],    'name' => $payload['name'],    'data_type' => $payload['data_type'] ?? 'mixed',    'is_required' => $payload['is_required'] ?? true,    'sequence' => $payload['sequence'] ?? $index + 1,    'created_at' => now(),    'updated_at' => now()]);
            }
        }
    }

    /*
     * =============================================================
     * Flows
     * =============================================================
     */

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importFlows(
        array $items
    ): int {
        foreach ($items as $item) {
            $code = $this->requiredCode(
                $item,
                'workflow'
            );

            DB::table('work_flows')->updateOrInsert(
                ['code' => $code],
                ['name' => $item['name'] ?? $this->titleFromCode($code),     'description' => $item['description'] ?? null,     'lifecycle_transition_type' => $item['lifecycle_transition_type'] ?? 'stay',     'next_lifecycle_phase_id' => isset($item['next_lifecycle_phase']) ? $this->id('work_lifecycle_phases', (string) $item['next_lifecycle_phase'], 'lifecycle phase') : null,     'business_object_id' => isset($item['business_object']) ? $this->id('work_business_objects', (string) $item['business_object'], 'business object') : null,     'lifecycle_phase_id' => isset($item['lifecycle_phase']) ? $this->id('work_lifecycle_phases', (string) $item['lifecycle_phase'], 'lifecycle phase') : null,     'version' => $item['version'] ?? 1,     'status' => $item['status'] ?? 'draft',     'updated_at' => now(),     'created_at' => now()]
            );

            $workflowId = $this->id(
                'work_flows',
                $code,
                'workflow'
            );

            $this->syncWorkflowRoles(
                $workflowId,
                $item
            );

            $this->syncSteps(
                $workflowId,
                $item['steps'] ?? []
            );
            if (($item['status'] ?? 'draft') === 'active') {
                $workflow = Flow::query()
                    ->findOrFail($workflowId);

                $this->workflowDefinitionValidator
                    ->validate($workflow);
            }
        }

        return count($items);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function syncWorkflowRoles(
        int $workflowId,
        array $item
    ): void {
        foreach (
            [
                'owner_roles' => 'work_flow_owners',
                'initiator_roles' => 'work_flow_initiators',
            ] as $key => $table
        ) {
            DB::table($table)->where('workflow_id', $workflowId)->delete();

            foreach ($item[$key] ?? [] as $roleCode) {
                DB::table($table)->insert(['workflow_id' => $workflowId,    'job_role_id' => $this->externalId('job_role', (string) $roleCode),    'created_at' => now(),    'updated_at' => now()]);
            }
        }
    }

    /*
     * =============================================================
     * Workflow Steps + Actions
     * =============================================================
     */

    /**
     * @param  array<int, array<string, mixed>>  $steps
     */
    private function syncSteps(
        int $workflowId,
        array $steps
    ): void {
        /*
         * First pass creates every Step so actions may safely
         * reference Steps declared later in the JSON document.
         */

        $stepIds = [];

        foreach (
            $steps as $index => $step
        ) {
            $code = $this->requiredCode(
                $step,
                'workflow step'
            );

            DB::table('work_flow_steps')->updateOrInsert([
                'workflow_id' => $workflowId,
                'code' => $code,
            ], [
                'work_package_id' => $this->id('work_packages', (string) $step['work_package'], 'work package'),
                'name' => $step['name'] ?? $this->titleFromCode($code),
                'step_type' => $step['step_type'],
                'sequence' => $step['sequence'] ?? $index + 1,
                'is_start' => $step['is_start'] ?? $index === 0,
                'updated_at' => now(),
                'created_at' => now(),
            ]);

            $stepIds[$code] = $this->stepId($workflowId, $code);
        }

        /*
         * Second pass rebuilds actions.
         */

        foreach ($steps as $step) {
            $stepCode = (string) $step['code'];

            $stepId = $stepIds[$stepCode];

            DB::table('work_flow_actions')->where('workflow_step_id', $stepId)->delete();

            foreach ($step['actions'] ?? [] as $index => $action) {
                $targetStepId = null;
                if (filled($action['target_step'] ?? null)) {
                    $targetCode = (string) $action['target_step'];
                    $targetStepId = $stepIds[$targetCode] ?? throw CatalogReferenceNotFound::for('workflow step', $targetCode);
                }

                DB::table('work_flow_actions')->insert(['workflow_step_id' => $stepId,    'action_type' => $action['action_type'],    'target_workflow_step_id' => $targetStepId,    'terminal_status' => $action['terminal_status'] ?? null,    'requires_confirmation' => $action['requires_confirmation'] ?? false,    'sequence' => $action['sequence'] ?? $index + 1,    'created_at' => now(),    'updated_at' => now()]);
            }
        }
    }

    /*
     * =============================================================
     * Lookup helpers
     * =============================================================
     */

    private function id(
        string $table,
        string $code,
        string $type
    ): int {
        $id = DB::table($table)
            ->where('code', $code)
            ->value('id');

        if ($id === null) {
            throw CatalogReferenceNotFound::for(
                $type,
                $code
            );
        }

        return (int) $id;
    }

    private function stepId(
        int $workflowId,
        string $code
    ): int {
        $id = DB::table('work_flow_steps')
            ->where(
                'workflow_id',
                $workflowId
            )
            ->where('code', $code)
            ->value('id');

        if ($id === null) {
            throw CatalogReferenceNotFound::for(
                'workflow step',
                $code
            );
        }

        return (int) $id;
    }

    private function externalId(
        string $kind,
        string $code
    ): int {
        $model = config(
            "bites.kerja.models.{$kind}"
        );

        if (
            ! is_string($model)
            || ! class_exists($model)
        ) {
            throw new RuntimeException(
                "Kerja external model [{$kind}] ".'is not configured.'
            );
        }

        /** @var class-string<Model> $model */
        return $this->modelId(
            $model,
            $code,
            str_replace('_', ' ', $kind)
        );
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function modelId(
        string $model,
        string $code,
        string $type
    ): int {
        $id = $model::query()
            ->where('code', $code)
            ->value('id');

        if ($id === null) {
            throw CatalogReferenceNotFound::for(
                $type,
                $code
            );
        }

        return (int) $id;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function requiredCode(
        array $item,
        string $type
    ): string {
        $code = $item['code'] ?? null;

        if (
            ! is_string($code)
            || blank($code)
        ) {
            throw new InvalidArgumentException(
                ucfirst($type).' requires a non-empty [code].'
            );
        }

        return $code;
    }

    private function titleFromCode(
        string $code
    ): string {
        return Str::of($code)
            ->replace(['_', '-'], ' ')
            ->lower()
            ->title()
            ->toString();
    }

    private function importEndpoints(array $items): int
    {
        foreach ($items as $item) {
            DB::table('work_endpoints')->updateOrInsert(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'base_url' => $item['base_url'],
                    'auth_profile' => $item['auth_profile'] ?? null,
                    'timeout_seconds' => $item['timeout_seconds'] ?? 30,
                    'is_active' => $item['is_active'] ?? true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return count($items);
    }

    private function importHooks(array $items): int
    {
        foreach ($items as $item) {
            DB::table('work_hooks')->updateOrInsert(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'operation' => $item['operation'],
                    'version' => $item['version'] ?? 1,
                    'schema' => json_encode($item['schema'] ?? [], JSON_THROW_ON_ERROR),
                    'configuration' => json_encode($item['configuration'] ?? [], JSON_THROW_ON_ERROR),
                    'is_active' => $item['is_active'] ?? true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return count($items);
    }

    private function importHookBindings(array $items): int
    {
        foreach ($items as $item) {
            [$type, $id] = $this->resolveHookable($item);
            DB::table('work_hook_bindings')->updateOrInsert(
                [
                    'hook_id' => $this->id('work_hooks', $item['hook'], 'hook'),
                    'hookable_type' => $type,
                    'hookable_id' => $id,
                    'hook_point' => $item['hook_point'],
                ],
                [
                    'sequence' => $item['sequence'] ?? 1,
                    'parameters' => isset($item['parameters'])
                        ? json_encode($item['parameters'], JSON_THROW_ON_ERROR)
                        : null,
                    'is_required' => $item['is_required'] ?? true,
                    'is_enabled' => $item['is_enabled'] ?? true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return count($items);
    }

    private function resolveHookable(array $item): array
    {
        return match ($item['hookable_type']) {
            'workflow' => [
                Flow::class,
                $this->id('work_flows', $item['workflow'], 'workflow'),
            ],
            'step' => [
                FlowStep::class,
                $this->stepId(
                    $this->id('work_flows', $item['workflow'], 'workflow'),
                    $item['step']
                ),
            ],
            'action' => [
                FlowAction::class,
                $this->actionId($item['workflow'], $item['step'], $item['action_type']),
            ],
            default => throw new InvalidArgumentException(
                "Unsupported hookable_type [{$item['hookable_type']}]."
            ),
        };
    }

    private function actionId(string $workflowCode, string $stepCode, string $actionType): int
    {
        $workflowId = $this->id('work_flows', $workflowCode, 'workflow');
        $stepId = $this->stepId($workflowId, $stepCode);

        return (int) (DB::table('work_flow_actions')
            ->where('workflow_step_id', $stepId)
            ->where('action_type', $actionType)
            ->value('id')
            ?? throw CatalogReferenceNotFound::for(
                'workflow action',
                $workflowCode.'.'.$stepCode.'.'.$actionType
            ));
    }
}
