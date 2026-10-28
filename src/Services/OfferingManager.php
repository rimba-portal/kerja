<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Actions\ExecuteOffering;
use Rimba\Work\Actions\PrepareOffering;
use Rimba\Work\Data\ExecutionResult;
use Rimba\Work\Data\PreparedOffering;
use Rimba\Work\Models\Offering;

class OfferingManager
{
    public function __construct(
        protected PrepareOffering $prepareOffering,
        protected ExecuteOffering $executeOffering,
    ) {}

    public function query(): Builder
    {
        return Offering::query()->with([
            'form',
            'workflow.lifecycle',
        ]);
    }

    public function available(
        int|string|null $orgUnitId = null,
    ): Collection {
        return $this->query()
            ->available()
            ->when(
                $orgUnitId,
                fn (Builder $query): Builder => $query->forOrgUnit(
                    $orgUnitId,
                ),
            )
            ->ordered()
            ->get();
    }

    public function find(
        string $code,
    ): Offering {
        return $this->query()
            ->where('code', $code)
            ->firstOrFail();
    }

    public function prepare(
        Offering|string $offering,
        array $metadata = [],
    ): PreparedOffering {
        return $this->prepareOffering->execute(
            offering: $this->resolveOffering($offering),
            metadata: $metadata,
        );
    }

    public function execute(
        Offering|string $offering,
        array $input = [],
        array $metadata = [],
        ?Model $subject = null,
        ?Model $formSubmission = null,
        bool $alreadyValidated = false,
        ?bool $queue = null,
    ): ExecutionResult {
        return $this->executeOffering->execute(
            offering: $this->resolveOffering($offering),
            input: $input,
            metadata: $metadata,
            existingSubject: $subject,
            formSubmission: $formSubmission,
            alreadyValidated: $alreadyValidated,
            queue: $queue,
        );
    }

    protected function resolveOffering(
        Offering|string $offering,
    ): Offering {
        return $offering instanceof Offering
            ? $offering
            : $this->find($offering);
    }
}
