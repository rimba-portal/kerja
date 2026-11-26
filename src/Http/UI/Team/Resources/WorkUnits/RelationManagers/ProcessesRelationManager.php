<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkUnits\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ProcessesRelationManager extends RelationManager
{
    protected static string $relationship = 'processHierarchyScopes';

    protected static ?string $title = 'Processes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('process.code')->label('Code')->searchable()->sortable(),
            TextColumn::make('process.name')->label('Process')->searchable()->sortable(),
            TextColumn::make('scope_level')->label('Scope level')->badge(),
            TextColumn::make('description')->limit(100)->toggleable(),
            TextColumn::make('attributes.source')->label('Source')->toggleable(),
        ]);
    }
}
