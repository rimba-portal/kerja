<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Equipment\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Floorplan\Models\Location;

final class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Equipment')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('code')->required()->maxLength(255)->unique(ignoreRecord: true),
                    Select::make('hierarchy_scope_id')
                        ->label('Work Unit')
                        ->relationship(
                            name: 'hierarchyScope',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query): Builder => $query
                                ->where('type', 'Work Unit')
                                ->orderBy('code'),
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (Location $record): string => trim("{$record->code} — {$record->name}"),
                        )
                        ->searchable(['code', 'name'])
                        ->preload()
                        ->nullable(),
                    Textarea::make('description')->rows(3)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Equipment attributes')
                ->schema([
                    TextInput::make('attributes.manufacturer')->maxLength(255),
                    TextInput::make('attributes.model')->maxLength(255),
                    TextInput::make('attributes.serial_number')->label('Serial number')->maxLength(255),
                    TextInput::make('attributes.state')->maxLength(255),
                    TextInput::make('attributes.area_code')->label('Area code')->maxLength(255),
                    TextInput::make('attributes.line_code')->label('Line code')->maxLength(255),
                    TextInput::make('attributes.process')->maxLength(255),
                    TextInput::make('attributes.product')->maxLength(255),
                    TextInput::make('attributes.secs_gem')->label('SECS/GEM')->maxLength(255),
                    TextInput::make('attributes.support_group')->label('Support group')->maxLength(255),
                    TextInput::make('attributes.source_inventory_id')->label('Source inventory ID')->maxLength(255),
                ])
                ->columns(2)
                ->collapsible(),
            Section::make('Additional metadata')
                ->schema([
                    KeyValue::make('attributes.metadata')
                        ->label('Metadata')
                        ->keyLabel('Property')
                        ->valueLabel('Value')
                        ->addActionLabel('Add property')
                        ->reorderable(),
                ])
                ->collapsed(),
        ]);
    }
}
