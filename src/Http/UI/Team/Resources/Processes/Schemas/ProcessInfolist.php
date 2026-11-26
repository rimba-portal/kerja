<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Processes\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ProcessInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Process')->schema([
                TextEntry::make('code'),
                TextEntry::make('name'),
                TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
                TextEntry::make('hierarchy_scopes_count')->label('Work Units')->state(
                    fn ($record): int => $record->hierarchyScopes()->count(),
                ),
            ])->columns(2),
            Section::make('Attributes')->schema([
                TextEntry::make('attributes.source')->placeholder('—'),
                TextEntry::make('attributes.raw_variants')->label('Raw variants')->badge(),
                TextEntry::make('attributes.observed_lines')->label('Observed lines')->badge(),
                TextEntry::make('attributes.observed_areas')->label('Observed areas')->badge(),
                TextEntry::make('attributes.observed_products')->label('Observed products')->badge(),
                KeyValueEntry::make('attributes.metadata')->label('Metadata')->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
