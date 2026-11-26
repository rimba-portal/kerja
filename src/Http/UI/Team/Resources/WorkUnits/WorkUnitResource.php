<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkUnits;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Floorplan\Models\Location;
use Rimba\Work\Http\UI\Staff\Pages\WorkUnit;
use Rimba\Work\Http\UI\Team\Resources\WorkUnits\Pages\ListWorkUnits;
use Rimba\Work\Http\UI\Team\Resources\WorkUnits\RelationManagers\EquipmentRelationManager;
use Rimba\Work\Http\UI\Team\Resources\WorkUnits\RelationManagers\ProcessesRelationManager;
use Rimba\Work\Http\UI\Team\Resources\WorkUnits\Schemas\WorkUnitForm;
use Rimba\Work\Http\UI\Team\Resources\WorkUnits\Schemas\WorkUnitInfolist;
use Rimba\Work\Http\UI\Team\Resources\WorkUnits\Tables\WorkUnitsTable;
use UnitEnum;

final class WorkUnitResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static ?string $navigationLabel = 'Work Units';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 50;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'Work Unit');
    }

    public static function form(Schema $schema): Schema
    {
        return WorkUnitForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkUnitInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkUnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EquipmentRelationManager::class,
            ProcessesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkUnits::route('/'),
            'view' => WorkUnit::route('/{record}'),
        ];
    }
}
