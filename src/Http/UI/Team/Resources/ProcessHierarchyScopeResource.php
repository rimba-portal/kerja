<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopeResource\Pages\CreateProcessHierarchyScope;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopeResource\Pages\EditProcessHierarchyScope;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopeResource\Pages\ListProcessHierarchyScopes;
use Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopeResource\Pages\ViewProcessHierarchyScope;
use Rimba\Work\Models\ProcessHierarchyScope;

class ProcessHierarchyScopeResource extends Resource
{
    protected static ?string $model = ProcessHierarchyScope::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Process hierarchy scope')
                ->schema([
                    Select::make('process_id')
                        ->relationship(name: 'process', titleAttribute: 'name')
                        ->searchable(['name', 'code'])->preload()->required(),
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
                ])->columns(2),
            Section::make('Attributes')
                ->schema([KeyValue::make('attributes')->columnSpanFull()])
                ->collapsible(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Process hierarchy scope')
                ->schema([
                    TextEntry::make('process.name'),
                    TextEntry::make('process.code')->label('Process code'),
                    TextEntry::make('hierarchyScope.name')->label('Work unit'),
                    TextEntry::make('hierarchyScope.code')->label('Work unit code'),
                    TextEntry::make('scope_level')->label('Scope level')->badge(),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('process.code')->label('Process code')->searchable()->sortable(),
                TextColumn::make('process.name')->label('Process')->searchable()->sortable(),
                TextColumn::make('hierarchyScope.code')->label('Work unit code')->searchable()->sortable(),
                TextColumn::make('hierarchyScope.name')->label('Work unit')->searchable()->sortable(),
                TextColumn::make('scope_level')->badge()->sortable(),
                TextColumn::make('description')->limit(80)->toggleable(),
            ])
            ->filters([
                SelectFilter::make('process_id')->relationship('process', 'name')->searchable()->preload(),
                SelectFilter::make('hierarchy_scope_id')
                    ->label('Work unit')->relationship('hierarchyScope', 'name')->searchable()->preload(),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcessHierarchyScopes::route('/'),
            'create' => CreateProcessHierarchyScope::route('/create'),
            'view' => ViewProcessHierarchyScope::route('/{record}'),
            'edit' => EditProcessHierarchyScope::route('/{record}/edit'),
        ];
    }
}
