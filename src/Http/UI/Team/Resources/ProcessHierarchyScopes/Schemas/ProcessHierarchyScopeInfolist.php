<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ProcessHierarchyScopeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Process scope')->schema([
                TextEntry::make('process.name')->label('Process'),
                TextEntry::make('hierarchyScope.name')->label('Work Unit'),
                TextEntry::make('scope_level')->label('Scope level')->badge(),
                TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
                TextEntry::make('attributes.source')->label('Source')->placeholder('—'),
                KeyValueEntry::make('attributes.metadata')->label('Metadata')->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
