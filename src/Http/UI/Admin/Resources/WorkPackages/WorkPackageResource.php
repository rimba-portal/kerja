<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkPackages;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Pages\ListWorkPackages;
use Rimba\Work\Models\WorkPackage;
use UnitEnum;

class WorkPackageResource extends Resource
{
    protected static ?string $model = WorkPackage::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 16;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
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
            'index' => ListWorkPackages::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Pages\CreateWorkPackage::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Pages\ViewWorkPackage::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Pages\EditWorkPackage::route('/{record}/edit'),
            //
        ];
    }
}
