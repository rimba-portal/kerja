<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowInitiators;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\WorkFlowInitiators\Pages\ListWorkFlowInitiators;
use Rimba\Work\Models\WorkFlowInitiator;
use UnitEnum;

class WorkFlowInitiatorResource extends Resource
{
    protected static ?string $model = WorkFlowInitiator::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 13;

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
            'index' => ListWorkFlowInitiators::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowInitiators\Pages\CreateWorkFlowInitiator::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowInitiators\Pages\ViewWorkFlowInitiator::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowInitiators\Pages\EditWorkFlowInitiator::route('/{record}/edit'),
            //
        ];
    }
}
