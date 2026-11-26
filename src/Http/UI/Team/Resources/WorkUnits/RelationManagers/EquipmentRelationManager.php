<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkUnits\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class EquipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'equipment';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->searchable()->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('attributes.manufacturer')->label('Manufacturer')->searchable(),
            TextColumn::make('attributes.model')->label('Model')->searchable(),
            TextColumn::make('attributes.serial_number')->label('Serial number')->searchable(),
            TextColumn::make('attributes.state')->label('State')->badge(),
            TextColumn::make('assets_count')->label('Assets')->counts('assets'),
        ]);
    }
}
