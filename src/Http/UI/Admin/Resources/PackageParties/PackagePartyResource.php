<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\PackageParties;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\PackageParties\Pages\ListPackageParties;
use Rimba\Work\Models\PackageParty;
use UnitEnum;

class PackagePartyResource extends Resource
{
    protected static ?string $model = PackageParty::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 17;

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
            'index' => ListPackageParties::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\PackageParties\Pages\CreatePackageParty::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\PackageParties\Pages\ViewPackageParty::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\PackageParties\Pages\EditPackageParty::route('/{record}/edit'),
            //
        ];
    }
}
