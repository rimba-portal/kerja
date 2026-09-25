<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Rimba\Work\Actions\CompleteTask;
use Rimba\Work\Enums\TaskStatus;
use Rimba\Work\Models\Task;
use Rimba\Work\Services\TaskInboxService;
use UnitEnum;

class MySipocTasks extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon =
        'heroicon-o-queue-list';

    protected static string|UnitEnum|null $navigationGroup =
        'ToDo';

    protected static ?string $navigationLabel =
        'My WorkPackages';

    protected static ?string $title =
        'My WorkPackages';

    protected string $view =
        'sipoc::staff.my-sipoc-tasks';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => app(TaskInboxService::class)
                    ->queryFor(auth()->user())
            )
            ->columns([
                TextColumn::make('name')
                    ->label('WorkPackage')
                    ->searchable()
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
                    ->visible(
                        fn (Task $record): bool => in_array($record->status, [
                            TaskStatus::Ready,
                            TaskStatus::Assigned,
                        ], true)
                    )
                    ->action(function (Task $record): void {
                        $record->update([
                            'status' => TaskStatus::Started,
                            'started_at' => now(),
                        ]);

                        Notification::make()
                            ->title('WorkPackage started')
                            ->success()
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
                            auth()->user()
                        );

                        Notification::make()
                            ->title('WorkPackage completed')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('assigned_at', 'desc');
    }
}
