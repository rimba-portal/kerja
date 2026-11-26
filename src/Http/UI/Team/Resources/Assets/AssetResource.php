<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Assets;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\Assets\Pages\CreateAsset;
use Rimba\Work\Http\UI\Team\Resources\Assets\Pages\EditAsset;
use Rimba\Work\Http\UI\Team\Resources\Assets\Pages\ListAssets;
use Rimba\Work\Http\UI\Team\Resources\Assets\Pages\ViewAsset;
use Rimba\Work\Http\UI\Team\Resources\Assets\Schemas\AssetForm;
use Rimba\Work\Http\UI\Team\Resources\Assets\Schemas\AssetInfolist;
use Rimba\Work\Http\UI\Team\Resources\Assets\Tables\AssetsTable;
use Rimba\Work\Models\Asset;
use UnitEnum;

final class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static ?string $navigationLabel = 'Assets';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return AssetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'view' => ViewAsset::route('/{record}'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
