<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkPackages;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Pages\CreateWorkPackage;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Pages\EditWorkPackage;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Pages\ListWorkPackages;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Pages\ViewWorkPackage;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Schemas\WorkPackageForm;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Schemas\WorkPackageInfolist;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackages\Tables\WorkPackagesTable;
use Rimba\Work\Models\WorkPackage;
use UnitEnum;

class WorkPackageResource extends Resource
{
    protected static ?string $model = WorkPackage::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowSmallRight;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return WorkPackageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkPackageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkPackagesTable::configure($table);
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
            'create' => CreateWorkPackage::route('/create'),
            'view' => ViewWorkPackage::route('/{record}'),
            'edit' => EditWorkPackage::route('/{record}/edit'),
        ];
    }
}
