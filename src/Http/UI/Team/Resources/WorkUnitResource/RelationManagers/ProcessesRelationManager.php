<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkUnitResource\RelationManagers;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProcessesRelationManager extends RelationManager
{
    protected static string $relationship = 'processHierarchyScopes';

    protected static ?string $title = 'Processes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('process_id')
                ->relationship(name: 'process', titleAttribute: 'name')
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
                TextColumn::make('process.code')->label('Process code')->searchable()->sortable(),
                TextColumn::make('process.name')->label('Process')->searchable()->sortable(),
                TextColumn::make('scope_level')->label('Scope')->badge(),
                TextColumn::make('description')->limit(80)->toggleable(),
            ]);
    }
}
