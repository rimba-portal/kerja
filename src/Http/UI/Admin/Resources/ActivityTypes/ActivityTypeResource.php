<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\ActivityTypes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\ActivityTypes\Pages\ListActivityTypes;
use Rimba\Work\Models\ActivityType;
use UnitEnum;

class ActivityTypeResource extends Resource
{
    protected static ?string $model = ActivityType::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 5;

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
            'index' => ListActivityTypes::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\ActivityTypes\Pages\CreateActivityType::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\ActivityTypes\Pages\ViewActivityType::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\ActivityTypes\Pages\EditActivityType::route('/{record}/edit'),
            //
        ];
    }
}
