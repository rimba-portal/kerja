<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkUnits\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class WorkUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('code')
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('parent.name')->label('Parent')->searchable()->placeholder('None'),
                TextColumn::make('equipment_count')->label('Equipment')->counts('equipment')->sortable(),
                TextColumn::make('process_hierarchy_scopes_count')->label('Processes')->counts('processHierarchyScopes')->sortable(),
            ])
            ->recordActions([ViewAction::make()])
            ->persistFiltersInSession();
    }
}
