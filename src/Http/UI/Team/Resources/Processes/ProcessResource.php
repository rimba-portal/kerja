<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Processes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\Processes\Pages\CreateProcess;
use Rimba\Work\Http\UI\Team\Resources\Processes\Pages\EditProcess;
use Rimba\Work\Http\UI\Team\Resources\Processes\Pages\ListProcesses;
use Rimba\Work\Http\UI\Team\Resources\Processes\Pages\ViewProcess;
use Rimba\Work\Http\UI\Team\Resources\Processes\RelationManagers\WorkUnitsRelationManager;
use Rimba\Work\Http\UI\Team\Resources\Processes\Schemas\ProcessForm;
use Rimba\Work\Http\UI\Team\Resources\Processes\Schemas\ProcessInfolist;
use Rimba\Work\Http\UI\Team\Resources\Processes\Tables\ProcessesTable;
use Rimba\Work\Models\Process;
use UnitEnum;

final class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static ?string $navigationLabel = 'Processes';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return ProcessForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProcessInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcessesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            WorkUnitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcesses::route('/'),
            'create' => CreateProcess::route('/create'),
            'view' => ViewProcess::route('/{record}'),
            'edit' => EditProcess::route('/{record}/edit'),
        ];
    }
}
