<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Equipment\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class EquipmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Equipment')->schema([
                TextEntry::make('code'),
                TextEntry::make('name'),
                TextEntry::make('hierarchyScope.name')->label('Work Unit')->placeholder('Unassigned'),
                TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
            ])->columns(2),
            Section::make('Attributes')->schema([
                TextEntry::make('attributes.manufacturer')->placeholder('—'),
                TextEntry::make('attributes.model')->placeholder('—'),
                TextEntry::make('attributes.serial_number')->label('Serial number')->placeholder('—'),
                TextEntry::make('attributes.state')->badge()->placeholder('—'),
                KeyValueEntry::make('attributes.metadata')->label('Metadata')->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
