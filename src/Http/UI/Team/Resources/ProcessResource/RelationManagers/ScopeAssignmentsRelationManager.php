<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ScopeAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'scopeAssignments';

    protected static ?string $title = 'Work units';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('hierarchy_scope_id')
                ->label('Work unit')
                ->relationship(name: 'hierarchyScope', titleAttribute: 'name')
                ->getOptionLabelFromRecordUsing(
                    fn ($record): string => trim("{$record->code} — {$record->name}", ' —'),
                )
                ->searchable(['name', 'code'])->preload()->required(),
            Select::make('scope_level')
                ->options(['Work Unit' => 'Work Unit'])
                ->default('Work Unit')->required(),
            Textarea::make('description')->columnSpanFull(),
            KeyValue::make('attributes')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hierarchyScope.code')->label('Work unit code')->searchable()->sortable(),
                TextColumn::make('hierarchyScope.name')->label('Work unit')->searchable()->sortable(),
                TextColumn::make('scope_level')->label('Scope')->badge(),
                TextColumn::make('description')->limit(80)->toggleable(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
