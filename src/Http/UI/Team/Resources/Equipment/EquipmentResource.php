<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Equipment;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\Equipment\Pages\CreateEquipment;
use Rimba\Work\Http\UI\Team\Resources\Equipment\Pages\EditEquipment;
use Rimba\Work\Http\UI\Team\Resources\Equipment\Pages\ListEquipment;
use Rimba\Work\Http\UI\Team\Resources\Equipment\Pages\ViewEquipment;
use Rimba\Work\Http\UI\Team\Resources\Equipment\RelationManagers\AssetsRelationManager;
use Rimba\Work\Http\UI\Team\Resources\Equipment\Schemas\EquipmentForm;
use Rimba\Work\Http\UI\Team\Resources\Equipment\Schemas\EquipmentInfolist;
use Rimba\Work\Http\UI\Team\Resources\Equipment\Tables\EquipmentTable;
use Rimba\Work\Models\Equipment;
use UnitEnum;

final class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static ?string $navigationLabel = 'Equipment';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return EquipmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EquipmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EquipmentTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AssetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEquipment::route('/'),
            'create' => CreateEquipment::route('/create'),
            'view' => ViewEquipment::route('/{record}'),
            'edit' => EditEquipment::route('/{record}/edit'),
        ];
    }
}
