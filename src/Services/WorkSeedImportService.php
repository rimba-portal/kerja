<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use JsonException;
use Rimba\Work\Models\Flow;
use Rimba\Work\Services\Import\CatalogImporter;
use Rimba\Work\Services\Import\FlowImporter;
use Rimba\Work\Services\Import\HookImporter;
use Rimba\Work\Services\Import\OrganizationImporter;
use Rimba\Work\Services\Import\PackageImporter;
use RuntimeException;

final class WorkSeedImportService
{
    public function __construct(
        private OrganizationImporter $organizationImporter,
        private CatalogImporter $catalogImporter,
        private PackageImporter $packageImporter,
        private FlowImporter $flowImporter,
        private HookImporter $hookImporter,
        private FlowDefinitionValidator $flowDefinitionValidator,
    ) {}

    /**
     * @return array<string, int>
     *
     * @throws JsonException
     */
    public function import(string $source): array
    {
        $payload = $this->loadJson($source);

        return DB::transaction(function () use ($payload): array {
            $counts = [];

            /*
             * External organizational vocabulary must exist before
             * Packages and Flows resolve their responsibility codes.
             */
            $counts += $this->organizationImporter->import($payload);

            /*
             * Kerja catalogs must exist before Package/Flow definitions.
             */
            $counts += $this->catalogImporter->import($payload);

            $counts['work_packages'] = $this->packageImporter->import(
                $payload['work_packages'] ?? [],
            );

            $counts['work_flows'] = $this->flowImporter->import(
                $payload['work_flows'] ?? [],
            );

            /*
             * Hooks are imported after their Flow/Step/Action hookables.
             */
            $counts += $this->hookImporter->import($payload);

            /*
             * Validate only after the complete definition graph exists,
             * including Endpoints, Hooks, and HookBindings.
             */
            $this->validateActiveFlows(
                $payload['work_flows'] ?? [],
            );

            return $counts;
        });
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    private function loadJson(string $source): array
    {
        $contents = str_starts_with($source, 'http://')
            || str_starts_with($source, 'https://')
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
            JSON_THROW_ON_ERROR,
        );

        if (! is_array($decoded)) {
            throw new RuntimeException(
                'Work catalog root must be a JSON object.'
            );
        }

        return $decoded;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function validateActiveFlows(array $items): void
    {
        foreach ($items as $item) {
            if (($item['status'] ?? 'draft') !== 'active') {
                continue;
            }

            $code = $item['code'] ?? null;

            if (! is_string($code) || blank($code)) {
                throw new RuntimeException(
                    'Active Flow definition requires a non-empty code.'
                );
            }

            $flow = Flow::query()
                ->where('code', $code)
                ->firstOrFail();

            $this->flowDefinitionValidator->validate($flow);
        }
    }
}
