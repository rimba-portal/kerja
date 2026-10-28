<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Workflows\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Rimba\Organization\Models\OrgUnit;
use Rimba\Work\Http\UI\Team\Resources\Workflows\WorkflowResource;
use Rimba\Work\Services\WorkAccessService;

class CreateWorkflow extends CreateRecord
{
    protected static string $resource = WorkflowResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data,
    ): array {
        $unit = OrgUnit::query()
            ->findOrFail(
                $data['org_unit_id']
            );

        abort_unless(
            app(
                WorkAccessService::class,
            )->canManageUnit(
                Filament::auth()->user(),
                $unit,
            ),
            403,
        );

        return $data;
    }
}
