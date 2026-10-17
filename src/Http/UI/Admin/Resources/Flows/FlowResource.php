<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Flows;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\Flows\Pages\ListFlows;
use Rimba\Work\Models\Flow;
use UnitEnum;

class FlowResource extends Resource
{
    protected static ?string $model = Flow::class;

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
            'index' => ListFlows::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\Flows\Pages\CreateFlow::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\Flows\Pages\ViewFlow::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\Flows\Pages\EditFlow::route('/{record}/edit'),
            //
        ];
    }
}
