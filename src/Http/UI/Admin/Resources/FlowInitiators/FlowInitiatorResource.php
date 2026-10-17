<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\FlowInitiators;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\FlowInitiators\Pages\ListFlowInitiators;
use Rimba\Work\Models\FlowInitiator;
use UnitEnum;

class FlowInitiatorResource extends Resource
{
    protected static ?string $model = FlowInitiator::class;

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
            'index' => ListFlowInitiators::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\FlowInitiators\Pages\CreateFlowInitiator::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\FlowInitiators\Pages\ViewFlowInitiator::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\FlowInitiators\Pages\EditFlowInitiator::route('/{record}/edit'),
            //
        ];
    }
}
