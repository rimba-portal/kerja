<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Processes\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ProcessForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Process')->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('code')->required()->maxLength(255)->unique(ignoreRecord: true),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ])->columns(2),
            Section::make('Observed attributes')->schema([
                TextInput::make('attributes.source')->maxLength(255),
                TagsInput::make('attributes.raw_variants')->label('Raw variants'),
                TagsInput::make('attributes.observed_lines')->label('Observed lines'),
                TagsInput::make('attributes.observed_areas')->label('Observed areas'),
                TagsInput::make('attributes.observed_products')->label('Observed products'),
            ])->columns(2)->collapsible(),
            Section::make('Additional metadata')->schema([
                KeyValue::make('attributes.metadata')
                    ->label('Metadata')
                    ->keyLabel('Property')
                    ->valueLabel('Value')
                    ->addActionLabel('Add property')
                    ->reorderable(),
            ])->collapsed(),
        ]);
    }
}
