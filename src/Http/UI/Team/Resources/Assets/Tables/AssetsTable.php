<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Assets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('code')
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('equipment.code')->label('Equipment')->searchable()->sortable(),
                TextColumn::make('attributes.inventory_number')->label('Inventory number')->searchable(),
                TextColumn::make('attributes.asset_class')->label('Asset class')->searchable()->toggleable(),
                TextColumn::make('attributes.cost_center')->label('Cost center')->searchable()->toggleable(),
                TextColumn::make('attributes.serial_number')->label('Serial number')->searchable()->toggleable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([CreateAction::make(), BulkActionGroup::make([DeleteBulkAction::make()])])
            ->persistFiltersInSession();
    }
}
