<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Offerings\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Staff\Pages\ExecuteOffering;
use Rimba\Work\Models\Offering;

class OfferingsTable
{
    public static function configure(
        Table $table,
    ): Table {
        return $table
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->copyable(),

                TextColumn::make('form.name')
                    ->label('Form')
                    ->placeholder('None'),

                TextColumn::make('workflow.name')
                    ->label('Workflow')
                    ->placeholder('None'),

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->placeholder('None'),

                TextColumn::make('availability')
                    ->badge(),

                TextColumn::make('executions_count')
                    ->counts('executions')
                    ->label('Executions'),

                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->recordActions([
                Action::make('preview')
                    ->icon('heroicon-m-eye')
                    ->url(
                        fn (
                            Offering $record,
                        ): string => ExecuteOffering::getUrl([
                            'offering' => $record->code,
                        ]),
                    )
                    ->openUrlInNewTab(),

                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
