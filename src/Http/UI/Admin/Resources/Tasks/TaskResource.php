<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Tasks;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\Tasks\Pages\CreateTask;
use Rimba\Work\Http\UI\Admin\Resources\Tasks\Pages\EditTask;
use Rimba\Work\Http\UI\Admin\Resources\Tasks\Pages\ListTasks;
use Rimba\Work\Http\UI\Admin\Resources\Tasks\Pages\ViewTask;
use Rimba\Work\Http\UI\Admin\Resources\Tasks\Schemas\TaskForm;
use Rimba\Work\Http\UI\Admin\Resources\Tasks\Schemas\TaskInfolist;
use Rimba\Work\Http\UI\Admin\Resources\Tasks\Tables\TasksTable;
use Rimba\Work\Models\Task;
use UnitEnum;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowSmallRight;

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaskInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
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
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'view' => ViewTask::route('/{record}'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
