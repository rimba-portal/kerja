<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Import;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class PackageImporter
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
            $code = $this->requiredCode(
                $item,
                'package',
            );

            $responsibleJobRole = $item['responsible_job_role']
                ?? $item['actor_job_role']
                ?? null;

            if (! is_string($responsibleJobRole) || blank($responsibleJobRole)) {
                throw new InvalidArgumentException(
                    "Package [{$code}] requires [responsible_job_role]."
                );
            }

            $orgTeam = $item['org_team'] ?? null;

            if (! is_string($orgTeam) || blank($orgTeam)) {
                throw new InvalidArgumentException(
                    "Package [{$code}] requires [org_team]."
                );
            }

            DB::table('work_packages')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $item['name']
                        ?? $this->titleFromCode($code),
                    'description' => $item['description'] ?? null,
                    'org_team_id' => $this->referenceResolver->externalId(
                        'org_team',
                        $orgTeam,
                    ),
                    'actor_job_role_id' => $this->referenceResolver->externalId(
                        'job_role',
                        $responsibleJobRole,
                    ),
                    'activity_type_id' => $this->referenceResolver->id(
                        'work_activity_types',
                        (string) $item['activity_type'],
                        'activity type',
                    ),
                    'business_object_id' => $this->referenceResolver->id(
                        'work_business_objects',
                        (string) $item['business_object'],
                        'business object',
                    ),
                    'version' => $item['version'] ?? 1,
                    'status' => $item['status'] ?? 'draft',
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );

            $packageId = $this->referenceResolver->id(
                'work_packages',
                $code,
                'package',
            );

            $this->syncParties($packageId, $item);
            $this->syncPayloads($packageId, $item);
        }

        return count($items);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function syncParties(
        int $packageId,
        array $item,
    ): void {
        DB::table('work_package_parties')
            ->where('work_package_id', $packageId)
            ->delete();

        foreach ([
            'suppliers' => 'supplier',
            'customers' => 'customer',
        ] as $key => $side) {
            foreach ($item[$key] ?? [] as $index => $value) {
                $party = is_array($value)
                    ? $value
                    : ['name' => $value];

                DB::table('work_package_parties')->insert([
                    'work_package_id' => $packageId,
                    'side' => $side,
                    'party_type' => $party['party_type'] ?? null,
                    'party_id' => $party['party_id'] ?? null,
                    'name' => $party['name'],
                    'sequence' => $party['sequence'] ?? $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function syncPayloads(
        int $packageId,
        array $item,
    ): void {
        DB::table('work_package_payloads')
            ->where('work_package_id', $packageId)
            ->delete();

        foreach ([
            'inputs' => 'input',
            'outputs' => 'output',
        ] as $key => $direction) {
            foreach ($item[$key] ?? [] as $index => $value) {
                $payload = is_array($value)
                    ? $value
                    : [
                        'code' => Str::of((string) $value)
                            ->snake()
                            ->upper()
                            ->toString(),
                        'name' => (string) $value,
                    ];

                DB::table('work_package_payloads')->insert([
                    'work_package_id' => $packageId,
                    'direction' => $direction,
                    'code' => $payload['code'],
                    'name' => $payload['name'],
                    'data_type' => $payload['data_type'] ?? 'mixed',
                    'is_required' => $payload['is_required'] ?? true,
                    'sequence' => $payload['sequence'] ?? $index + 1,
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
