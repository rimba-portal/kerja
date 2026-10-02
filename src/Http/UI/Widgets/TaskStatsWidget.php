<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Rimba\Work\Models\Task;

final class MyTaskStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $staff = Auth::user()?->staff;

        $builder = Task::query()
            ->whereMorphedTo('assignee', $staff);

        return [
            Stat::make(
                'Assigned',
                (clone $builder)
                    ->where('status', 'assigned')
                    ->count()
            )
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('info'),

            Stat::make(
                'Started',
                (clone $builder)
                    ->where('status', 'started')
                    ->count()
            )
                ->icon(Heroicon::OutlinedClock)
                ->color('warning'),

            Stat::make(
                'Completed',
                (clone $builder)
                    ->where('status', 'completed')
                    ->count()
            )
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),

            Stat::make(
                'Due Soon',
                (clone $builder)
                    ->whereNotNull('due_at')
                    ->where('due_at', '<=', now()->addDays(3))
                    ->whereIn('status', [
                        'assigned',
                        'started',
                    ])
                    ->count()
            )
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color('danger'),
        ];
    }
}
