<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Rimba\Work\Contracts\WorkflowTaskProvider;

class MyPendingTasks extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $count = app(WorkflowTaskProvider::class)
            ->assignedQuery(
                Filament::auth()->user(),
            )
            ->count();

        return [
            Stat::make(
                'Tasks awaiting you',
                $count,
            )
                ->icon(
                    'heroicon-o-clipboard-document-check'
                )
                ->color(
                    $count > 0
                        ? 'warning'
                        : 'success',
                ),
        ];
    }
}
