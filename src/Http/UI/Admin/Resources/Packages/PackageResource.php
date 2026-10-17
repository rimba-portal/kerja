<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Packages;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\Packages\Pages\ListPackages;
use Rimba\Work\Models\Package;
use UnitEnum;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

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
            'index' => ListPackages::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\Packages\Pages\CreatePackage::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\Packages\Pages\ViewPackage::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\Packages\Pages\EditPackage::route('/{record}/edit'),
            //
        ];
    }
}
