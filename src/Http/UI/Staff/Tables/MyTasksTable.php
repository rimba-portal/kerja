<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Tables;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Contracts\WorkflowTaskProvider;

class MyTasksTable
{
    public static function configure(
        Table $table,
        Builder $query,
        WorkflowTaskProvider $provider,
    ): Table {
        return $table
            ->query($query)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Task')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Assigned')
                    ->since(),

                TextColumn::make('due_at')
                    ->label('Due')
                    ->dateTime()
                    ->placeholder('No due date'),
            ])
            ->recordActions([
                Action::make('perform')
                    ->label('Take action')
                    ->icon(
                        'heroicon-m-check-circle'
                    )
                    ->form(
                        fn (
                            Model $record,
                        ): array => self::actionForm(
                            provider: $provider,
                            task: $record,
                        ),
                    )
                    ->action(
                        function (
                            Model $record,
                            array $data,
                        ) use ($provider): void {
                            $provider->perform(
                                task: $record,
                                action: $data['action'],
                                user: Filament::auth()->user(),
                                data: $data,
                            );

                            Notification::make()
                                ->success()
                                ->title('Task updated')
                                ->send();
                        },
                    ),
            ]);
    }

    protected static function actionForm(
        WorkflowTaskProvider $provider,
        Model $task,
    ): array {
        $actions = $provider->actions(
            task: $task,
            user: Filament::auth()->user(),
        );

        return [
            Select::make(
                'action'
            )
                ->options($actions)
                ->required(),

            Textarea::make(
                'comment'
            )
                ->rows(3),
        ];
    }
}
