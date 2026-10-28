<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Forms\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Rimba\Work\Models\Form;

class FormsTable
{
    public static function configure(
        Table $table,
    ): Table {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('provider')
                    ->badge(),

                TextColumn::make('display_mode')
                    ->badge(),

                TextColumn::make('offerings_count')
                    ->counts('offerings')
                    ->label('Offerings'),

                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->recordActions([
                Action::make('design')
                    ->label('Open designer')
                    ->icon('heroicon-m-paint-brush')
                    ->url(
                        fn (
                            Form $record,
                        ): string => self::designerUrl(
                            $record,
                        ),
                    )
                    ->openUrlInNewTab(),

                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    protected static function designerUrl(
        Form $record,
    ): string {
        return route(
            'filament.team.resources.forms.edit',
            [
                'record' => $record->provider_reference
                    ?? $record->provider_key,
            ],
        );
    }
}
