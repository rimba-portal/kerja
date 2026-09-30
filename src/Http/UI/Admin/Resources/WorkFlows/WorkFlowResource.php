<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlows;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\WorkFlows\Pages\ListWorkFlows;
use Rimba\Work\Models\WorkFlow;
use UnitEnum;

class WorkFlowResource extends Resource
{
    protected static ?string $model = WorkFlow::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 10;

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
            'index' => ListWorkFlows::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlows\Pages\CreateWorkFlow::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlows\Pages\ViewWorkFlow::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlows\Pages\EditWorkFlow::route('/{record}/edit'),
            //
        ];
    }
}
