<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class WorkSeedImportService
{
    public function import(string $source): array
    {
        $payload = $this->loadJson($source);

        return DB::transaction(
            function () use ($payload): array {

                return [
                    'activity_types' => $this->importActivityTypes(
                        $payload['activity_types'] ?? []
                    ),

                    'business_objects' => $this->importBusinessObjects(
                        $payload['business_objects'] ?? []
                    ),

                    'work_packages' => $this->importWorkPackages(
                        $payload['work_packages'] ?? []
                    ),

                    'work_flows' => $this->importWorkFlows(
                        $payload['work_flows'] ?? []
                    ),
                ];
            }
        );
    }

    protected function loadJson(
        string $source
    ): array {

        $contents = str_starts_with(
            $source,
            'http'
        )
            ? file_get_contents($source)
            : File::get($source);

        $decoded = json_decode(
            $contents,
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

    protected function importActivityTypes(
        array $items
    ): int {

        foreach ($items as $item) {

            DB::table('work_activity_types')
                ->updateOrInsert(
                    [
                        'code' => $item['code'],
                    ],
                    [
                        'name' => $item['name'],
                        'description' => $item['description']
                                ?? null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
        }

        return count($items);
    }

    protected function importBusinessObjects(
        array $items
    ): int {

        foreach ($items as $item) {

            DB::table('work_business_objects')
                ->updateOrInsert(
                    [
                        'code' => $item['code'],
                    ],
                    [
                        'name' => $item['name'],
                        'model_class' => $item['model_class']
                                ?? null,
                        'description' => $item['description']
                                ?? null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
        }

        return count($items);
    }

    protected function importWorkPackages(
        array $items
    ): int {

        foreach ($items as $item) {

            $id = DB::table(
                'work_packages'
            )->updateOrInsert(
                [
                    'code' => $item['code'],
                ],
                [
                    'name' => $item['name'],
                    'description' => $item['description']
                            ?? null,
                    'org_team_id' => $this->resolveOrgTeam(
                        $item['org_team']
                    ),
                    'actor_job_role_id' => $this->resolveJobRole(
                        $item['actor_job_role']
                    ),
                    'activity_type_id' => $this->resolveActivityType(
                        $item['activity_type']
                    ),
                    'business_object_id' => $this->resolveBusinessObject(
                        $item['business_object']
                    ),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $this->syncSipoc(
                $item
            );
        }

        return count($items);
    }

    protected function importWorkFlows(
        array $items
    ): int {

        foreach ($items as $flow) {

            $workflowId =
                $this->upsertWorkFlow(
                    $flow
                );

            $this->syncInitiators(
                $workflowId,
                $flow
            );

            $this->syncFormFields(
                $workflowId,
                $flow
            );

            $this->syncSteps(
                $workflowId,
                $flow
            );
        }

        return count($items);
    }

    protected function syncSipoc(
        array $definition
    ): void {
        //
        // Import:
        //
        // suppliers
        // inputs
        // outputs
        // customers
        //
    }

    protected function syncInitiators(
        int $workflowId,
        array $definition
    ): void {
        //
    }

    protected function syncFormFields(
        int $workflowId,
        array $definition
    ): void {
        //
    }

    protected function syncSteps(
        int $workflowId,
        array $definition
    ): void {
        //
    }

    protected function upsertWorkFlow(
        array $definition
    ): int {
        //
        // create/update work_flows
        //
        return 1;
    }

    protected function resolveActivityType(
        string $code
    ): int {
        return (int)
            DB::table(
                'work_activity_types'
            )
                ->where(
                    'code',
                    $code
                )
                ->value('id');
    }

    protected function resolveBusinessObject(
        string $code
    ): int {
        return (int)
            DB::table(
                'work_business_objects'
            )
                ->where(
                    'code',
                    $code
                )
                ->value('id');
    }

    protected function resolveOrgTeam(
        string $code
    ): int {
        //
        // resolve OrgTeam
        //
        return 1;
    }

    protected function resolveJobRole(
        string $code
    ): int {
        //
        // resolve JobRole
        //
        return 1;
    }
}
