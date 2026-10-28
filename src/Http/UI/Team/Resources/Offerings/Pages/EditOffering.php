<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Offerings\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Rimba\Organization\Models\OrgUnit;
use Rimba\Work\Http\UI\Team\Resources\Offerings\OfferingResource;
use Rimba\Work\Services\WorkAccessService;

class EditOffering extends EditRecord
{
    protected static string $resource =
        OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(
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
