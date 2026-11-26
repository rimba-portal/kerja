<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Equipment\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class EquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('code')
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('hierarchyScope.code')->label('Work Unit')->searchable()->sortable()->placeholder('Unassigned'),
                TextColumn::make('attributes.manufacturer')->label('Manufacturer')->searchable()->toggleable(),
                TextColumn::make('attributes.model')->label('Model')->searchable()->toggleable(),
                TextColumn::make('attributes.serial_number')->label('Serial number')->searchable()->toggleable(),
                TextColumn::make('attributes.state')->label('State')->badge(),
                TextColumn::make('assets_count')->label('Assets')->counts('assets')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                CreateAction::make(),
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->persistFiltersInSession();
    }
}
