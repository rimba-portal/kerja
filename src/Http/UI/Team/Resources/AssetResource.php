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
use Rimba\Work\Http\UI\Team\Resources\AssetResource\Pages\CreateAsset;
use Rimba\Work\Http\UI\Team\Resources\AssetResource\Pages\EditAsset;
use Rimba\Work\Http\UI\Team\Resources\AssetResource\Pages\ListAssets;
use Rimba\Work\Http\UI\Team\Resources\AssetResource\Pages\ViewAsset;
use Rimba\Work\Models\Asset;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Asset')
                ->schema([
                    Select::make('equipment_id')
                        ->relationship(name: 'equipment', titleAttribute: 'name')
                        ->getOptionLabelFromRecordUsing(
                            fn ($record): string => trim("{$record->code} — {$record->name}", ' —'),
                        )
                        ->searchable(['name', 'code'])
                        ->preload()
                        ->required(),
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('code')->required()->maxLength(255)->unique(ignoreRecord: true),
                    TextInput::make('inventory_number')->label('Inventory number')->maxLength(255),
                    TextInput::make('main_asset_number')->label('Main asset number')->maxLength(255),
                    TextInput::make('asset_class')->label('Asset class')->maxLength(255),
                    TextInput::make('cost_center')->label('Cost center')->maxLength(255),
                    TextInput::make('group')->maxLength(255),
                    TextInput::make('serial_number')->label('Serial number')->maxLength(255),
                    Textarea::make('description')->columnSpanFull(),
                    Textarea::make('description2')->label('Description 2')->columnSpanFull(),
                ])->columns(2),
            Section::make('Attributes')
                ->schema([KeyValue::make('attributes')->columnSpanFull()])
                ->collapsible(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Asset')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('code'),
                    TextEntry::make('equipment.name'),
                    TextEntry::make('equipment.code')->label('Equipment code'),
                    TextEntry::make('inventory_number')->label('Inventory number')->placeholder('—'),
                    TextEntry::make('main_asset_number')->label('Main asset number')->placeholder('—'),
                    TextEntry::make('asset_class')->label('Asset class')->placeholder('—'),
                    TextEntry::make('cost_center')->label('Cost center')->placeholder('—'),
                    TextEntry::make('group')->placeholder('—'),
                    TextEntry::make('serial_number')->label('Serial number')->placeholder('—'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                    TextEntry::make('description2')->label('Description 2')->columnSpanFull()->placeholder('—'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('equipment.code')->label('Equipment')->searchable()->sortable(),
                TextColumn::make('inventory_number')->label('Inventory #')->searchable()->toggleable(),
                TextColumn::make('main_asset_number')->label('Main asset #')->searchable()->toggleable(),
                TextColumn::make('asset_class')->searchable()->toggleable(),
                TextColumn::make('cost_center')->searchable()->toggleable(),
                TextColumn::make('group')->searchable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('asset_class')->options(
                    fn (): array => Asset::query()
                        ->whereNotNull('asset_class')->distinct()->orderBy('asset_class')
                        ->pluck('asset_class', 'asset_class')->all(),
                ),
                SelectFilter::make('equipment_id')
                    ->relationship('equipment', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'view' => ViewAsset::route('/{record}'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
