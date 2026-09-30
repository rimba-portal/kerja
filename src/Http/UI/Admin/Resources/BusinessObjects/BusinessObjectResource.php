<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\BusinessObjects;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\BusinessObjects\Pages\ListBusinessObjects;
use Rimba\Work\Models\BusinessObject;
use UnitEnum;

class BusinessObjectResource extends Resource
{
    protected static ?string $model = BusinessObject::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 7;

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
            'index' => ListBusinessObjects::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\BusinessObjects\Pages\CreateBusinessObject::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\BusinessObjects\Pages\ViewBusinessObject::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\BusinessObjects\Pages\EditBusinessObject::route('/{record}/edit'),
            //
        ];
    }
}
