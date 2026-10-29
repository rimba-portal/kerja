<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\OfferingExecutions\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class OfferingExecutionsTable
{
    public static function configure(
        Table $table,
    ): Table {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('offering.name')
                    ->label('Offering')
                    ->searchable(),

                TextColumn::make('staff.name')
                    ->label('Requested by')
                    ->placeholder('Unknown'),

                TextColumn::make('orgUnit.name')
                    ->label('Unit'),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime()
                    ->placeholder('In progress'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
