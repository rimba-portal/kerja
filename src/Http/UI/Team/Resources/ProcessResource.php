<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\ProcessResource\Pages\CreateProcess;
use Rimba\Work\Http\UI\Team\Resources\ProcessResource\Pages\EditProcess;
use Rimba\Work\Http\UI\Team\Resources\ProcessResource\Pages\ListProcesses;
use Rimba\Work\Http\UI\Team\Resources\ProcessResource\Pages\ViewProcess;
use Rimba\Work\Http\UI\Team\Resources\ProcessResource\RelationManagers\ScopeAssignmentsRelationManager;
use Rimba\Work\Models\Process;

class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Process')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('code')->required()->maxLength(255)->unique(ignoreRecord: true),
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
            Section::make('Process')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('code'),
                    TextEntry::make('scope_assignments_count')->state(
                        fn (Process $record): int => $record->scopeAssignments()->count(),
                    )->label('Work units'),
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
                TextColumn::make('description')->limit(80)->searchable()->toggleable(),
                TextColumn::make('scope_assignments_count')
                    ->counts('scopeAssignments')->label('Work units')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [ScopeAssignmentsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcesses::route('/'),
            'create' => CreateProcess::route('/create'),
            'view' => ViewProcess::route('/{record}'),
            'edit' => EditProcess::route('/{record}/edit'),
        ];
    }
}
