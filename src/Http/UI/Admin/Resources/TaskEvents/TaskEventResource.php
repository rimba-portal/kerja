<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\TaskEvents;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages\ListTaskEvents;
use Rimba\Work\Models\TaskEvent;
use UnitEnum;

class TaskEventResource extends Resource
{
    protected static ?string $model = TaskEvent::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 9;

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
            'index' => ListTaskEvents::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages\CreateTaskEvent::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages\ViewTaskEvent::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\TaskEvents\Pages\EditTaskEvent::route('/{record}/edit'),
            //
        ];
    }
}
