<?php

namespace Rimba\Work\Http\UI\Admin\Resources\TaskEvents;

use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TaskEventResource extends Resource
{
    protected static ?string $model = \Rimba\Work\Models\TaskEvent::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 9;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema { return $schema->components([]); }

    public static function infolist(Schema $schema): Schema { return $schema->components([]); }

    public static function table(Table $table): Table { return $table->columns([]); }

    public static function getRelations(): array 
    { 
        return [ 
            // 
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages\ListTaskEvents::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages\CreateTaskEvent::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages\ViewTaskEvent::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages\EditTaskEvent::route('/{record}/edit'),
            //
        ];
    }
}
