<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\EquipmentResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('code')->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('inventory_number')->label('Inventory number')->maxLength(255),
            TextInput::make('main_asset_number')->label('Main asset number')->maxLength(255),
            TextInput::make('asset_class')->label('Asset class')->maxLength(255),
            TextInput::make('cost_center')->label('Cost center')->maxLength(255),
            TextInput::make('group')->maxLength(255),
            TextInput::make('serial_number')->label('Serial number')->maxLength(255),
            Textarea::make('description')->columnSpanFull(),
            Textarea::make('description2')->label('Description 2')->columnSpanFull(),
            KeyValue::make('attributes')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('inventory_number')->label('Inventory #')->searchable(),
                TextColumn::make('main_asset_number')->label('Main asset #')->searchable(),
                TextColumn::make('asset_class')->label('Class')->searchable(),
                TextColumn::make('cost_center')->label('Cost center')->searchable(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
