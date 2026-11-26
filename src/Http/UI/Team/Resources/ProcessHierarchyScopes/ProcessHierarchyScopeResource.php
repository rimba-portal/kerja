<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Pages\CreateProcessHierarchyScope;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Pages\EditProcessHierarchyScope;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Pages\ListProcessHierarchyScopes;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Pages\ViewProcessHierarchyScope;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Schemas\ProcessHierarchyScopeForm;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Schemas\ProcessHierarchyScopeInfolist;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Tables\ProcessHierarchyScopesTable;
use Rimba\Work\Models\ProcessHierarchyScope;
use UnitEnum;

final class ProcessHierarchyScopeResource extends Resource
{
    protected static ?string $model = ProcessHierarchyScope::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static ?string $navigationLabel = 'Process Scopes';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return ProcessHierarchyScopeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProcessHierarchyScopeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcessHierarchyScopesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcessHierarchyScopes::route('/'),
            'create' => CreateProcessHierarchyScope::route('/create'),
            'view' => ViewProcessHierarchyScope::route('/{record}'),
            'edit' => EditProcessHierarchyScope::route('/{record}/edit'),
        ];
    }
}
