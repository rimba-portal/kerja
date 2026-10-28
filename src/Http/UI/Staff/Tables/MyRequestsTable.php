<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Http\UI\Staff\Pages\ViewWorkItem;
use Rimba\Work\Models\OfferingExecution;

class MyRequestsTable
{
    public static function configure(
        Table $table,
        Builder $query,
    ): Table {
        return $table
            ->query($query)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('offering.name')
                    ->label('Request')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('subject_id')
                    ->label('Record')
                    ->formatStateUsing(
                        fn (
                            mixed $state,
                            OfferingExecution $record,
                        ): string => $record->subject
                            ? class_basename($record->subject)
                                .' #'
                                .$record->subject->getKey()
                            : 'Pending',
                    ),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->since()
                    ->placeholder('In progress'),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(
                        fn (
                            OfferingExecution $record,
                        ): string => ViewWorkItem::getUrl([
                            'execution' => $record->getKey(),
                        ]),
                    ),
            ]);
    }
}
