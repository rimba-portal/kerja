<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Rimba\Work\Actions\CompleteTask;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Services\TaskInboxService;
use UnitEnum;

class WorkTasks extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup ='ToDo';

    protected static ?string $navigationLabel ='My WorkPackages';

    protected static ?string $title ='My WorkPackages';

    protected string $view ='bites::staff.work-tasks';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                app(TaskInboxService::class)
                    ->queryFor(auth()->user())
            )
            ->columns([
                TextColumn::make('name')
                    ->label('WorkPackage')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('activity_type')
                    ->badge(),

                TextColumn::make('business_object')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('due_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('start')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->visible(
                        fn (Task $record): bool => in_array(
                            $record->status,
                            [
                                TaskStatus::Ready,
                                TaskStatus::Assigned,
                            ],
                            true,
                        )
                    )
                    ->action(function (Task $record): void {
                        $record->update([
                            'status' => TaskStatus::Started,
                            'started_at' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('WorkPackage started')
                            ->send();
                    }),

                Action::make('complete')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(
                        fn (Task $record): bool => $record->status === TaskStatus::Started
                    )
                    ->action(function (Task $record): void {
                        app(CompleteTask::class)->execute(
                            $record,
                            [],
                            auth()->user(),
                        );

                        Notification::make()
                            ->success()
                            ->title('WorkPackage completed')
                            ->send();
                    }),
            ])
            ->defaultSort('assigned_at', 'desc');
    }
}
