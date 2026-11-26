<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Floorplan\Models\Location;
use Rimba\Work\Http\UI\Staff\Pages\WorkUnit;
use Rimba\Work\Http\UI\Team\Resources\WorkUnitResource\Pages\ListWorkUnits;
use Rimba\Work\Http\UI\Team\Resources\WorkUnitResource\RelationManagers\EquipmentRelationManager;
use Rimba\Work\Http\UI\Team\Resources\WorkUnitResource\RelationManagers\ProcessesRelationManager;

class WorkUnitResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'Work Unit');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Work unit')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('code'),
                    TextEntry::make('type')->badge(),
                    TextEntry::make('parent.name')->label('Parent')->placeholder('—'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('parent.code')->label('Work center')->searchable()->sortable(),
                TextColumn::make('equipment_count')->counts('equipment')->label('Equipment')->sortable(),
                TextColumn::make('process_hierarchy_scopes_count')
                    ->counts('processHierarchyScopes')->label('Processes')->sortable(),
            ])
            ->recordUrl(fn (Location $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [
            EquipmentRelationManager::class,
            ProcessesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkUnits::route('/'),
            'view' => WorkUnit::route('/{record}'),
        ];
    }
}
