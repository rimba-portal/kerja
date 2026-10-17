<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\FlowActions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\FlowActions\Pages\ListFlowActions;
use Rimba\Work\Models\FlowAction;
use UnitEnum;

class FlowActionResource extends Resource
{
    protected static ?string $model = FlowAction::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 11;

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
            'index' => ListFlowActions::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\FlowActions\Pages\CreateFlowAction::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\FlowActions\Pages\ViewFlowAction::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\FlowActions\Pages\EditFlowAction::route('/{record}/edit'),
            //
        ];
    }
}
