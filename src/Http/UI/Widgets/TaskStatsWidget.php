<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Widgets;

use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Rimba\Work\Models\TaskInstance;

class TaskStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $staffId = Auth::user()?->staff?->id;

        return [
            Stat::make(
                'Assigned Tasks',
                TaskInstance::query()
                    ->where('assigned_to_id', $staffId)
                    ->count(),
            )
                ->description('Tasks assigned to you')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('info'),

            Stat::make(
                'Pending Tasks',
                TaskInstance::query()
                    ->where('assigned_to_id', $staffId)
                    ->where('is_completed', false)
                    ->count(),
            )
                ->description('Awaiting completion')
                ->icon(Heroicon::OutlinedClock)
                ->color('warning'),

            Stat::make(
                'Completed Tasks',
                TaskInstance::query()
                    ->where('assigned_to_id', $staffId)
                    ->where('is_completed', true)
                    ->count(),
            )
                ->description('Finished tasks')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success'),

            Stat::make(
                'Completed By Me',
                TaskInstance::query()
                    ->where('completed_by_id', $staffId)
                    ->count(),
            )
                ->description('Tasks completed by you')
                ->icon(Heroicon::OutlinedUser)
                ->color('primary'),
        ];
    }
}
