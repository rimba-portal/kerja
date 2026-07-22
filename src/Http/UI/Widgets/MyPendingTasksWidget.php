<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Widgets;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;
use Rimba\Work\Models\TaskInstance;

class MyPendingTasksWidget extends TableWidget
{
    protected static ?string $heading = 'My Pending Tasks';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TaskInstance::query()
                    ->where('assigned_to_id', Auth::user()?->staff?->id)
                    ->where('is_completed', false)
            )
            ->columns([
                Tables\Columns\TextColumn::make('task.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('checklistInstance.name')
                    ->label('Checklist')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->since(),
            ])
            ->recordActions([
                Actions\Action::make('open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (TaskInstance $record): string => route(
                        'filament.staff.resources.task-instances.edit',
                        $record
                    )),
            ]);
    }
}
