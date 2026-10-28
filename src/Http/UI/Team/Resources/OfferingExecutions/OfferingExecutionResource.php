<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\OfferingExecutions;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Http\UI\Team\Resources\OfferingExecutions\Pages\ListOfferingExecutions;
use Rimba\Work\Http\UI\Team\Resources\OfferingExecutions\Pages\ViewOfferingExecution;
use Rimba\Work\Http\UI\Team\Resources\OfferingExecutions\Schemas\OfferingExecutionInfolist;
use Rimba\Work\Http\UI\Team\Resources\OfferingExecutions\Tables\OfferingExecutionsTable;
use Rimba\Work\Models\OfferingExecution;
use Rimba\Work\Services\WorkAccessService;

class OfferingExecutionResource extends Resource
{
    protected static ?string $model = OfferingExecution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return OfferingExecutionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfferingExecutionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOfferingExecutions::route('/'),
            'view' => ViewOfferingExecution::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $unitIds = app(
            WorkAccessService::class,
        )->manageableUnitIds(
            Filament::auth()->user(),
        );

        return parent::getEloquentQuery()
            ->whereIn(
                'org_unit_id',
                $unitIds,
            );
    }
}
