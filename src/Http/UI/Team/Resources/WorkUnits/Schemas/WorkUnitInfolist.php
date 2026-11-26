<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkUnits\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class WorkUnitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Work Unit')->schema([
                TextEntry::make('code'),
                TextEntry::make('name'),
                TextEntry::make('type')->badge(),
                TextEntry::make('parent.name')->label('Parent')->placeholder('No parent'),
                TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
            ])->columns(2),
        ]);
    }
}
