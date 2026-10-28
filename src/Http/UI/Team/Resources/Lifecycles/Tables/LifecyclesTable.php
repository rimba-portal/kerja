<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Lifecycles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LifecyclesTable
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
                    ->searchable()
                    ->copyable(),

                TextColumn::make('category')
                    ->badge(),

                TextColumn::make('parent.name')
                    ->placeholder('Root'),

                TextColumn::make('phases_count')
                    ->counts('phases')
                    ->label('Phases'),

                TextColumn::make('workflows_count')
                    ->counts('workflows')
                    ->label('Workflows'),

                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
