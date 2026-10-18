<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;
use Rimba\Work\Models\Task;

final class MyPendingTasksWidget extends TableWidget
{
    protected static ?string $heading = 'My Pending Tasks';

    public function table(Table $table): Table
    {
        $staff = Auth::user()?->staff;

        return $table
            ->query(
                Task::query()
                    ->whereMorphedTo(
                        'assignee',
                        $staff,
                    )
                    ->whereIn('status', [
                        'assigned',
                        'started',
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Work Package')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('flowStep.name')
                    ->label('Step')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->since()
                    ->label('Assigned'),
            ])
            ->recordActions([]);
    }
}
