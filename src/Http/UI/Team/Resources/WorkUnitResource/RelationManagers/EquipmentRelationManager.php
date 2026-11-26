<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkUnitResource\RelationManagers;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'equipment';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('code')->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('state')->maxLength(255),
            TextInput::make('manufacturer')->maxLength(255),
            TextInput::make('model')->maxLength(255),
            TextInput::make('serial_number')->label('Serial number')->maxLength(255),
            Textarea::make('description')->columnSpanFull(),
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
                TextColumn::make('manufacturer')->searchable()->toggleable(),
                TextColumn::make('model')->searchable()->toggleable(),
                TextColumn::make('serial_number')->label('Serial')->searchable()->toggleable(),
                TextColumn::make('state')->badge(),
                TextColumn::make('assets_count')->counts('assets')->label('Assets')->sortable(),
            ]);
    }
}
