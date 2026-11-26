<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Processes\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Schemas\ProcessHierarchyScopeForm;

final class WorkUnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'hierarchyScopes';

    protected static ?string $title = 'Work Units';

    public function form(Schema $schema): Schema
    {
        return ProcessHierarchyScopeForm::configureForProcess($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hierarchyScope.code')->label('Work Unit')->searchable()->sortable(),
                TextColumn::make('hierarchyScope.name')->label('Name')->searchable(),
                TextColumn::make('scope_level')->label('Scope level')->badge(),
                TextColumn::make('description')->limit(80)->toggleable(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
