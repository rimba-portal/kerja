<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\Pages\ListWorkPackagePartys;
use Rimba\Work\Models\WorkPackageParty;
use UnitEnum;

class WorkPackagePartyResource extends Resource
{
    protected static ?string $model = WorkPackageParty::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 17;

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
            'index' => ListWorkPackagePartys::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\Pages\CreateWorkPackageParty::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\Pages\ViewWorkPackageParty::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\WorkPackagePartys\Pages\EditWorkPackageParty::route('/{record}/edit'),
            //
        ];
    }
}
