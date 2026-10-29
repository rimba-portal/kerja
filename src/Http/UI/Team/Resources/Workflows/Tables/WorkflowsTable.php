<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Workflows\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Rimba\Work\Models\Workflow;
use Rimba\Work\Services\WorkflowManager;

class WorkflowsTable
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
                    ->copyable(),

                TextColumn::make('lifecycle_code')
                    ->label('Lifecycle')
                    ->placeholder('None')
                    ->searchable(),

                TextColumn::make('lifecycle_phase_code')
                    ->label('Phase')
                    ->placeholder('None')
                    ->searchable(),

                TextColumn::make('provider')
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
                    ->icon('heroicon-m-share')
                    ->url(
                        fn (Workflow $record): ?string => app(WorkflowManager::class)
                            ->designerUrl($record),
                    )
                    ->visible(
                        fn (Workflow $record): bool => filled(
                            app(WorkflowManager::class)
                                ->designerUrl($record)
                        ),
                    )
                    ->openUrlInNewTab(),

                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
