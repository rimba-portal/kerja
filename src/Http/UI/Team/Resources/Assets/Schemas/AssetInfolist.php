<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Assets\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class AssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Asset')->schema([
                TextEntry::make('code'),
                TextEntry::make('name'),
                TextEntry::make('equipment.name')->label('Equipment'),
                TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
            ])->columns(2),
            Section::make('Attributes')->schema([
                TextEntry::make('attributes.inventory_number')->label('Inventory number')->placeholder('—'),
                TextEntry::make('attributes.asset_class')->label('Asset class')->placeholder('—'),
                TextEntry::make('attributes.cost_center')->label('Cost center')->placeholder('—'),
                TextEntry::make('attributes.cost_center_description')->label('Cost center description')->placeholder('—'),
                TextEntry::make('attributes.serial_number')->label('Serial number')->placeholder('—'),
                KeyValueEntry::make('attributes.metadata')->label('Metadata')->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
