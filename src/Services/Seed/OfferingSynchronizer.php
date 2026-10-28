<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Illuminate\Support\Facades\DB;
use Rimba\Work\Contracts\SeedSynchronizer;
use Rimba\Work\Contracts\SubjectRegistry;
use Rimba\Work\Exceptions\SeedSynchronizationException;
use Rimba\Work\Models\Form;
use Rimba\Work\Models\Offering;
use Rimba\Work\Models\Workflow;

class OfferingSynchronizer implements SeedSynchronizer
{
    public function __construct(
        protected SubjectRegistry $subjects,
    ) {}

    public function sync(
        array $records,
    ): void {
        DB::transaction(function () use ($records): void {
            foreach ($records as $record) {
                $this->syncOffering(
                    $record,
                );
            }
        });
    }

    protected function syncOffering(
        array $record,
    ): Offering {
        $code = $record['code'];

        $orgUnitCode =
            $record['org_unit_code']
            ?? throw new SeedSynchronizationException(
                "Offering [{$code}] is missing org_unit_code."
            );

        $orgUnitId =
            $this->resolveOrgUnitId(
                $orgUnitCode,
            );

        $formCode =
            $record['form_code']
            ?? null;

        $workflowCode =
            $record['workflow_code']
            ?? null;

        $subjectType =
            $record['subject_type']
            ?? null;

        $this->validateForm(
            code: $formCode,
            orgUnitId: $orgUnitId,
        );

        $this->validateWorkflow(
            code: $workflowCode,
            orgUnitId: $orgUnitId,
        );

        $this->validateSubject(
            $subjectType,
        );

        return Offering::query()->updateOrCreate(
            [
                'code' => $code,
            ],
            [
                'org_unit_id' => $orgUnitId,

                'name' => $record['name'],

                'description' => $record['description']
                    ?? null,

                'form_code' => $formCode,

                'workflow_code' => $workflowCode,

                'subject_type' => $subjectType,

                'subject_creation_mode' => $record['subject_creation_mode']
                    ?? 'create',

                'input_mapping' => $record['input_mapping']
                    ?? null,

                'context_mapping' => $record['context_mapping']
                    ?? null,

                'defaults' => $record['defaults']
                    ?? null,

                'visibility' => $record['visibility']
                    ?? 'team',

                'availability' => $record['availability']
                    ?? 'available',

                'icon' => $record['icon']
                    ?? null,

                'color' => $record['color']
                    ?? null,

                'sort' => $record['sort']
                    ?? 0,

                'is_active' => $record['is_active']
                    ?? true,

                'settings' => $record['settings']
                    ?? null,
            ],
        );
    }

    protected function validateForm(
        ?string $code,
        int|string $orgUnitId,
    ): void {
        if ($code === null) {
            return;
        }

        $form = Form::query()
            ->where('code', $code)
            ->first();

        if (! $form) {
            throw new SeedSynchronizationException(
                "Form [{$code}] does not exist."
            );
        }

        if (
            (string) $form->org_unit_id
            !== (string) $orgUnitId
        ) {
            throw new SeedSynchronizationException(
                "Form [{$code}] belongs to a different OrgUnit."
            );
        }
    }

    protected function validateWorkflow(
        ?string $code,
        int|string $orgUnitId,
    ): void {
        if ($code === null) {
            return;
        }

        $workflow = Workflow::query()
            ->where('code', $code)
            ->first();

        if (! $workflow) {
            throw new SeedSynchronizationException(
                "Workflow [{$code}] does not exist."
            );
        }

        if (
            (string) $workflow->org_unit_id
            !== (string) $orgUnitId
        ) {
            throw new SeedSynchronizationException(
                "Workflow [{$code}] belongs to a different OrgUnit."
            );
        }
    }

    protected function validateSubject(
        ?string $alias,
    ): void {
        if ($alias === null) {
            return;
        }

        if (! $this->subjects->has($alias)) {
            throw new SeedSynchronizationException(
                "Subject [{$alias}] is not registered."
            );
        }
    }

    protected function resolveOrgUnitId(
        string $code,
    ): int|string {
        $model = config(
            'work.models.org_unit',
        );

        if (! $model) {
            throw new SeedSynchronizationException(
                'work.models.org_unit is not configured.'
            );
        }

        $unit = $model::query()
            ->where('code', $code)
            ->first();

        if (! $unit) {
            throw new SeedSynchronizationException(
                "OrgUnit [{$code}] does not exist."
            );
        }

        return $unit->getKey();
    }
}
