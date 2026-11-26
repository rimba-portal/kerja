<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Assets\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Rimba\Work\Models\Equipment;

final class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return self::schema($schema, includeEquipment: true);
    }

    public static function configureForEquipment(Schema $schema): Schema
    {
        return self::schema($schema, includeEquipment: false);
    }

    private static function schema(Schema $schema, bool $includeEquipment): Schema
    {
        $identity = [
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('code')->required()->maxLength(255)->unique(ignoreRecord: true),
        ];

        if ($includeEquipment) {
            $identity[] = Select::make('equipment_id')
                ->label('Equipment')
                ->relationship(name: 'equipment', titleAttribute: 'name')
                ->getOptionLabelFromRecordUsing(
                    fn (Equipment $record): string => trim("{$record->code} — {$record->name}"),
                )
                ->searchable(['code', 'name'])
                ->preload()
                ->required();
        }

        $identity[] = Textarea::make('description')->rows(3)->columnSpanFull();

        return $schema->components([
            Section::make('Asset')->schema($identity)->columns(2),
            Section::make('Asset attributes')->schema([
                TextInput::make('attributes.inventory_number')->label('Inventory number')->maxLength(255),
                TextInput::make('attributes.main_asset_number')->label('Main asset number')->maxLength(255),
                TextInput::make('attributes.asset_class')->label('Asset class')->maxLength(255),
                TextInput::make('attributes.description2')->label('Description 2')->maxLength(255),
                TextInput::make('attributes.cost_center')->label('Cost center')->maxLength(255),
                TextInput::make('attributes.cost_center_description')->label('Cost center description')->maxLength(255),
                TextInput::make('attributes.group')->maxLength(255),
                TextInput::make('attributes.asset_main_number_text')->label('Asset main number text')->maxLength(255),
                TextInput::make('attributes.serial_number')->label('Serial number')->maxLength(255),
                TextInput::make('attributes.source_inventory_id')->label('Source inventory ID')->maxLength(255),
            ])->columns(2)->collapsible(),
            Section::make('Additional metadata')->schema([
                KeyValue::make('attributes.metadata')
                    ->label('Metadata')
                    ->keyLabel('Property')
                    ->valueLabel('Value')
                    ->addActionLabel('Add property')
                    ->reorderable(),
            ])->collapsed(),
        ]);
    }
}
