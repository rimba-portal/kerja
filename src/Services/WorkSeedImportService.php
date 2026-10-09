<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Rimba\Work\Exceptions\CatalogReferenceNotFound;
use RuntimeException;

final class WorkSeedImportService
{
    public function import(string $source): array
    {
        $p = $this->loadJson($source);

        return DB::transaction(function () use ($p): array {
            $counts = [];
            foreach (['activity_types' => 'work_activity_types', 'business_objects' => 'work_business_objects', 'lifecycles' => 'work_lifecycles', 'lifecycle_phases' => 'work_lifecycle_phases'] as $key => $table) {
                $counts[$key] = $this->importSimple($table, $p[$key] ?? []);
            }

            $counts['work_packages'] = $this->importWorkPackages($p['work_packages'] ?? []);
            $counts['work_flows'] = $this->importWorkFlows($p['work_flows'] ?? []);

            return $counts;
        });
    }

    private function loadJson(string $source): array
    {
        $contents = str_starts_with($source, 'http') ? file_get_contents($source) : File::get($source);
        $decoded = json_decode((string) $contents, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($decoded)) {
            throw new RuntimeException('Invalid JSON structure.');
        }

        return $decoded;
    }

    private function importSimple(string $table, array $items): int
    {
        foreach ($items as $item) {
            $row = $item;
            unset($row['code']);
            foreach (['business_object' => 'business_object_id', 'lifecycle' => 'lifecycle_id'] as $key => $fk) {
                if (isset($row[$key])) {
                    $row[$fk] = $this->id($key === 'lifecycle' ? 'work_lifecycles' : 'work_business_objects', $row[$key], $key);
                    unset($row[$key]);
                }
            }

            DB::table($table)->updateOrInsert(['code' => $item['code']], $row + ['updated_at' => now(), 'created_at' => now()]);
        }

        return count($items);
    }

    private function importWorkPackages(array $items): int
    {
        foreach ($items as $item) {
            DB::table('work_packages')->updateOrInsert(['code' => $item['code']], [
                'name' => $item['name'], 'description' => $item['description'] ?? null,
                'org_team_id' => $this->externalId('org_team', $item['org_team']),
                'actor_job_role_id' => $this->externalId('job_role', $item['responsible_job_role'] ?? $item['actor_job_role']),
                'activity_type_id' => $this->id('work_activity_types', $item['activity_type'], 'activity type'),
                'business_object_id' => $this->id('work_business_objects', $item['business_object'], 'business object'),
                'version' => $item['version'] ?? 1, 'status' => $item['status'] ?? 'draft', 'updated_at' => now(), 'created_at' => now(),
            ]);
            $id = $this->id('work_packages', $item['code'], 'work package');
            DB::table('work_package_parties')->where('work_package_id', $id)->delete();
            foreach (['suppliers' => 'supplier', 'customers' => 'customer'] as $key => $side) {
                foreach ($item[$key] ?? [] as $i => $v) {
                    DB::table('work_package_parties')->insert(['work_package_id' => $id, 'side' => $side, 'name' => is_array($v) ? $v['name'] : $v, 'sequence' => $i + 1, 'created_at' => now(), 'updated_at' => now()]);
                }
            }

            DB::table('work_package_payloads')->where('work_package_id', $id)->delete();
            foreach (['inputs' => 'input', 'outputs' => 'output'] as $key => $direction) {
                foreach ($item[$key] ?? [] as $i => $v) {
                    $v = is_array($v) ? $v : ['code' => str($v)->snake()->upper()->value(), 'name' => $v];
                    DB::table('work_package_payloads')->insert(['work_package_id' => $id, 'direction' => $direction, 'code' => $v['code'], 'name' => $v['name'], 'data_type' => $v['data_type'] ?? 'mixed', 'is_required' => $v['is_required'] ?? true, 'sequence' => $i + 1, 'created_at' => now(), 'updated_at' => now()]);
                }
            }
        }

        return count($items);
    }

    private function importWorkFlows(array $items): int
    {
        foreach ($items as $item) {
            DB::table('work_flows')->updateOrInsert(['code' => $item['code']], [
                'name' => $item['name'], 'description' => $item['description'] ?? null,
                'business_object_id' => isset($item['business_object']) ? $this->id('work_business_objects', $item['business_object'], 'business object') : null,
                'lifecycle_phase_id' => isset($item['lifecycle_phase']) ? $this->id('work_lifecycle_phases', $item['lifecycle_phase'], 'lifecycle phase') : null,
                'version' => $item['version'] ?? 1, 'status' => $item['status'] ?? 'draft', 'updated_at' => now(), 'created_at' => now(),
            ]);
            $wid = $this->id('work_flows', $item['code'], 'workflow');
            foreach (['owner_roles' => 'work_flow_owners', 'initiator_roles' => 'work_flow_initiators'] as $key => $table) {
                DB::table($table)->where('workflow_id', $wid)->delete();
                foreach ($item[$key] ?? [] as $role) {
                    DB::table($table)->insert(['workflow_id' => $wid, 'job_role_id' => $this->externalId('job_role', $role), 'created_at' => now(), 'updated_at' => now()]);
                }
            }

            DB::table('work_flow_form_fields')->where('workflow_id', $wid)->delete();
            foreach ($item['form_fields'] ?? [] as $i => $f) {
                DB::table('work_flow_form_fields')->insert(['workflow_id' => $wid, 'code' => $f['code'], 'name' => $f['name'], 'field_type' => $f['field_type'], 'help_text' => $f['help_text'] ?? null, 'default_value' => $f['default_value'] ?? null, 'is_required' => $f['is_required'] ?? false, 'sequence' => $f['sequence'] ?? $i + 1, 'created_at' => now(), 'updated_at' => now()]);
            }

            $this->syncSteps($wid, $item['steps'] ?? []);
        }

        return count($items);
    }

    private function syncSteps(int $wid, array $steps): void
    {
        $codes = [];
        foreach ($steps as $i => $s) {
            DB::table('work_flow_steps')->updateOrInsert(['workflow_id' => $wid, 'code' => $s['code']], [
                'work_package_id' => $this->id('work_packages', $s['work_package'], 'work package'), 'name' => $s['name'], 'sequence' => $s['sequence'] ?? $i + 1, 'is_start' => $s['is_start'] ?? $i === 0, 'updated_at' => now(), 'created_at' => now()]);
            $codes[$s['code']] = $this->stepId($wid, $s['code']);
        }

        foreach ($steps as $step) {
            $sid = $codes[$step['code']];
            DB::table('work_flow_actions')->where('workflow_step_id', $sid)->delete();
            foreach ($step['actions'] ?? [] as $i => $a) {
                DB::table('work_flow_actions')->insert([
                    'workflow_step_id' => $sid, 'code' => $a['code'], 'name' => $a['name'], 'style' => $a['style'] ?? 'primary',
                    'target_workflow_step_id' => isset($a['target_step']) ? ($codes[$a['target_step']] ?? throw CatalogReferenceNotFound::for('workflow step', $a['target_step'])) : null,
                    'completion_effect' => $a['completion_effect'] ?? 'continue', 'condition' => isset($a['condition']) ? json_encode($a['condition']) : null,
                    'requires_confirmation' => $a['requires_confirmation'] ?? false, 'sequence' => $a['sequence'] ?? $i + 1, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    private function id(string $table, string $code, string $type): int
    {
        return (int) (DB::table($table)->where('code', $code)->value('id') ?? throw CatalogReferenceNotFound::for($type, $code));
    }

    private function stepId(int $wid, string $code): int
    {
        return (int) DB::table('work_flow_steps')->where('workflow_id', $wid)->where('code', $code)->value('id');
    }

    private function externalId(string $kind, string $code): int
    {
        $model = config("bites.kerja.models.{$kind}");

        return (int) ($model::query()->where('code',$code)->value('id') ?? throw CatalogReferenceNotFound::for($kind,$code));
    }
}
