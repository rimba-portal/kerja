<?php

namespace Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys;

use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkPackagePartyResource extends Resource
{
    protected static ?string $model = \Rimba\Work\Models\WorkPackageParty::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 17;

    protected static ?string $recordTitleAttribute = 'id';

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
            'index' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\Pages\ListWorkPackagePartys::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\Pages\CreateWorkPackageParty::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\Pages\ViewWorkPackageParty::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\Pages\EditWorkPackageParty::route('/{record}/edit'),
            //
        ];
    }
}
