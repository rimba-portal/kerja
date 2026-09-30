<?php

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowInstances;

use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkFlowInstanceResource extends Resource
{
    protected static ?string $model = \Rimba\Work\Models\WorkFlowInstance::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 14;

    protected static ?string $recordTitleAttribute = 'status';

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
            'index' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowInstances\Pages\ListWorkFlowInstances::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowInstances\Pages\CreateWorkFlowInstance::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowInstances\Pages\ViewWorkFlowInstance::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowInstances\Pages\EditWorkFlowInstance::route('/{record}/edit'),
            //
        ];
    }
}
