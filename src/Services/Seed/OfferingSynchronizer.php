<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Seed;

use Illuminate\Support\Facades\DB;
use Rimba\Work\Contracts\SubjectRegistry;
use Rimba\Work\Models\Form;
use Rimba\Work\Models\Offering;
use Rimba\Work\Models\Workflow;
use RuntimeException;

class OfferingSynchronizer
{
    public function __construct(
        protected SubjectRegistry $subjects,
    ) {}

    public function sync(
        array $records,
    ): void {
        DB::transaction(function () use ($records): void {
            foreach ($records as $record) {
                $this->syncOffering($record);
            }
        });
    }

    protected function syncOffering(
        array $record,
    ): Offering {
        $formCode = $record['form_code'] ?? null;

        $workflowCode = $record['workflow_code'] ?? null;

        $subjectType = $record['subject_type'] ?? null;

        $this->validateForm($formCode);

        $this->validateWorkflow($workflowCode);

        $this->validateSubject($subjectType);

        return Offering::query()->updateOrCreate(
            [
                'code' => $record['code'],
            ],
            [
                'name' => $record['name'],

                'description' => $record['description'] ?? null,

                /*
                 * Team resolution should be performed separately from an
                 * environment-specific database ID.
                 */
                'org_team_id' => $this->resolveTeam(
                    $record['team_code'] ?? null,
                ),

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

                'icon' => $record['icon'] ?? null,

                'color' => $record['color'] ?? null,

                'sort' => $record['sort'] ?? 0,

                'is_active' => $record['is_active'] ?? true,

                'settings' => $record['settings'] ?? null,
            ],
        );
    }

    protected function validateForm(
        ?string $code,
    ): void {
        if ($code === null) {
            return;
        }

        if (
            ! Form::query()
                ->where('code', $code)
                ->exists()
        ) {
            throw new RuntimeException(
                "Form [{$code}] does not exist."
            );
        }
    }

    protected function validateWorkflow(
        ?string $code,
    ): void {
        if ($code === null) {
            return;
        }

        if (
            ! Workflow::query()
                ->where('code', $code)
                ->exists()
        ) {
            throw new RuntimeException(
                "Workflow [{$code}] does not exist."
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
            throw new RuntimeException(
                "Subject [{$alias}] is not registered."
            );
        }
    }

    protected function resolveTeam(
        ?string $teamCode,
    ): ?string {
        if ($teamCode === null) {
            return null;
        }

        $modelClass = config(
            'work.models.org_team',
        );

        if (! $modelClass) {
            throw new RuntimeException(
                "Unable to resolve team [{$teamCode}]: work.models.org_team is not configured."
            );
        }

        $team = $modelClass::query()
            ->where('code', $teamCode)
            ->first();

        if (! $team) {
            throw new RuntimeException(
                "OrgTeam [{$teamCode}] does not exist."
            );
        }

        return (string) $team->getKey();
    }
}
