<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ProcessHierarchyScopesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('process.code')->label('Process')->searchable()->sortable(),
                TextColumn::make('hierarchyScope.code')->label('Work Unit')->searchable()->sortable(),
                TextColumn::make('hierarchyScope.name')->label('Work Unit name')->searchable()->toggleable(),
                TextColumn::make('scope_level')->label('Scope level')->badge(),
                TextColumn::make('attributes.source')->label('Source')->toggleable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([CreateAction::make(), BulkActionGroup::make([DeleteBulkAction::make()])])
            ->persistFiltersInSession();
    }
}
