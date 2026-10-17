<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\PackagePayloads;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\PackagePayloads\Pages\ListPackagePayloads;
use Rimba\Work\Models\PackagePayload;
use UnitEnum;

class PackagePayloadResource extends Resource
{
    protected static ?string $model = PackagePayload::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 18;

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
            'index' => ListPackagePayloads::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\PackagePayloads\Pages\CreatePackagePayload::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\PackagePayloads\Pages\ViewPackagePayload::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\PackagePayloads\Pages\EditPackagePayload::route('/{record}/edit'),
            //
        ];
    }
}
