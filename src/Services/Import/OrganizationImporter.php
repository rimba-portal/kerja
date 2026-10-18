<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Import;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Rimba\Organization\Models\OrgTeam;
use Rimba\Organization\Models\OrgUnit;
use Rimba\Position\Models\JobRole;

final class OrganizationImporter
{
    public function __construct(
        private ReferenceResolver $referenceResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, int>
     */
    public function import(array $payload): array
    {
        return [
            'org_units' => $this->importOrgUnits(
                $payload['org_units'] ?? [],
            ),
            'org_teams' => $this->importOrgTeams(
                $payload['org_teams'] ?? [],
            ),
            'job_roles' => $this->importJobRoles(
                $payload['job_roles'] ?? [],
            ),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function importOrgUnits(array $items): int
    {
        /*
         * First pass creates every unit so parent references may be
         * resolved regardless of declaration order.
         */
        foreach ($items as $item) {
            $code = $this->requiredCode(
                $item,
                'organizational unit',
            );

            $unit = OrgUnit::query()
                ->where('code', $code)
                ->first();

            if (! $unit) {
                OrgUnit::query()->create([
                    'uuid' => (string) Str::uuid(),
                    'code' => $code,
                    'name' => $item['name']
                        ?? $this->titleFromCode($code),
                    'description' => $item['description'] ?? null,
                    'attributes' => $item['attributes'] ?? null,
                    'org_corp_id' => null,
                    'parent_id' => null,
                ]);

                continue;
            }

            /*
             * Kerja bootstraps missing organizational vocabulary but
             * does not overwrite organization-owned master data.
             */
            $changes = [];

            if (blank($unit->name)) {
                $changes['name'] = $item['name']
                    ?? $this->titleFromCode($code);
            }

            if (
                blank($unit->description)
                && array_key_exists('description', $item)
            ) {
                $changes['description'] = $item['description'];
            }

            if (
                empty($unit->attributes)
                && array_key_exists('attributes', $item)
            ) {
                $changes['attributes'] = $item['attributes'];
            }

            if ($changes !== []) {
                $unit->update($changes);
            }
        }

        /*
         * Second pass resolves optional parent codes.
         */
        foreach ($items as $item) {
            if (blank($item['parent'] ?? null)) {
                continue;
            }

            $code = $this->requiredCode(
                $item,
                'organizational unit',
            );

            $unit = OrgUnit::query()
                ->where('code', $code)
                ->firstOrFail();

            if ($unit->parent_id !== null) {
                continue;
            }

            $unit->update([
                'parent_id' => $this->referenceResolver->modelId(
                    OrgUnit::class,
                    (string) $item['parent'],
                    'organizational unit',
                ),
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
                'organizational team',
            );

            $orgUnitCode = $item['org_unit'] ?? null;

            if (! is_string($orgUnitCode) || blank($orgUnitCode)) {
                throw new InvalidArgumentException(
                    "OrgTeam [{$code}] requires [org_unit]."
                );
            }

            $orgUnitId = $this->referenceResolver->modelId(
                OrgUnit::class,
                $orgUnitCode,
                'organizational unit',
            );

            $team = OrgTeam::query()
                ->where('code', $code)
                ->first();

            if (! $team) {
                OrgTeam::query()->create([
                    'code' => $code,
                    'name' => $item['name']
                        ?? $this->titleFromCode($code),
                    'org_unit_id' => $orgUnitId,
                    'is_active' => $item['is_active'] ?? true,
                    'attributes' => $item['attributes'] ?? null,
                ]);

                continue;
            }

            $changes = [];

            if (blank($team->name)) {
                $changes['name'] = $item['name']
                    ?? $this->titleFromCode($code);
            }

            if (
                empty($team->attributes)
                && array_key_exists('attributes', $item)
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
                'job role',
            );

            $role = JobRole::query()
                ->where('code', $code)
                ->first();

            if (! $role) {
                JobRole::query()->create([
                    'code' => $code,
                    'name' => $item['name']
                        ?? $this->titleFromCode($code),
                    'type' => $item['type'] ?? null,
                    'description' => $item['description'] ?? null,
                    'attributes' => $item['attributes'] ?? null,
                ]);

                continue;
            }

            $changes = [];

            if (blank($role->name)) {
                $changes['name'] = $item['name']
                    ?? $this->titleFromCode($code);
            }

            if (
                blank($role->type)
                && array_key_exists('type', $item)
            ) {
                $changes['type'] = $item['type'];
            }

            if (
                blank($role->description)
                && array_key_exists('description', $item)
            ) {
                $changes['description'] = $item['description'];
            }

            if (
                empty($role->attributes)
                && array_key_exists('attributes', $item)
            ) {
                $changes['attributes'] = $item['attributes'];
            }

            if ($changes !== []) {
                $role->update($changes);
            }
        }

        return count($items);
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
