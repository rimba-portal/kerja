<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\FlowInstances;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\FlowInstances\Pages\ListFlowInstances;
use Rimba\Work\Models\FlowInstance;
use UnitEnum;

class FlowInstanceResource extends Resource
{
    protected static ?string $model = FlowInstance::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 14;

    protected static ?string $recordTitleAttribute = 'status';

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
            'index' => ListFlowInstances::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\FlowInstances\Pages\CreateFlowInstance::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\FlowInstances\Pages\ViewFlowInstance::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\FlowInstances\Pages\EditFlowInstance::route('/{record}/edit'),
            //
        ];
    }
}
