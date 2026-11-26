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
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\EquipmentResource\Pages\CreateEquipment;
use Rimba\Work\Http\UI\Team\Resources\EquipmentResource\Pages\EditEquipment;
use Rimba\Work\Http\UI\Team\Resources\EquipmentResource\Pages\ListEquipment;
use Rimba\Work\Http\UI\Team\Resources\EquipmentResource\Pages\ViewEquipment;
use Rimba\Work\Http\UI\Team\Resources\EquipmentResource\RelationManagers\AssetsRelationManager;
use Rimba\Work\Models\Equipment;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Equipment')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('code')->required()->maxLength(255)->unique(ignoreRecord: true),
                    Select::make('hierarchy_scope_id')
                        ->label('Work unit')
                        ->relationship(name: 'hierarchyScope', titleAttribute: 'name')
                        ->getOptionLabelFromRecordUsing(
                            fn ($record): string => trim("{$record->code} — {$record->name}", ' —'),
                        )
                        ->searchable(['name', 'code'])
                        ->preload(),
                    TextInput::make('state')->maxLength(255),
                    TextInput::make('manufacturer')->maxLength(255),
                    TextInput::make('model')->maxLength(255),
                    TextInput::make('serial_number')->label('Serial number')->maxLength(255),
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
            Section::make('Equipment')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('code'),
                    TextEntry::make('hierarchyScope.name')->label('Work unit'),
                    TextEntry::make('hierarchyScope.code')->label('Work unit code'),
                    TextEntry::make('state')->placeholder('—'),
                    TextEntry::make('manufacturer')->placeholder('—'),
                    TextEntry::make('model')->placeholder('—'),
                    TextEntry::make('serial_number')->label('Serial number')->placeholder('—'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ])->columns(2),
            Section::make('Source attributes')
                ->schema([
                    TextEntry::make('attributes')
                        ->label('')
                        ->formatStateUsing(
                            fn (?array $state): string => json_encode(
                                $state ?? [],
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
                            ) ?: '{}',
                        )
                        ->columnSpanFull(),
                ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('hierarchyScope.code')->label('Work unit')->searchable()->sortable(),
                TextColumn::make('manufacturer')->searchable()->toggleable(),
                TextColumn::make('model')->searchable()->toggleable(),
                TextColumn::make('serial_number')->label('Serial')->searchable()->toggleable(),
                TextColumn::make('state')->badge()->searchable(),
                TextColumn::make('assets_count')->counts('assets')->label('Assets')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('state')->options(
                    fn (): array => Equipment::query()
                        ->whereNotNull('state')->distinct()->orderBy('state')->pluck('state', 'state')->all(),
                ),
                SelectFilter::make('hierarchy_scope_id')
                    ->label('Work unit')
                    ->relationship('hierarchyScope', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [AssetsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEquipment::route('/'),
            'create' => CreateEquipment::route('/create'),
            'view' => ViewEquipment::route('/{record}'),
            'edit' => EditEquipment::route('/{record}/edit'),
        ];
    }
}
