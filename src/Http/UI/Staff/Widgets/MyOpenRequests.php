<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Rimba\Work\Enums\ExecutionStatus;
use Rimba\Work\Models\OfferingExecution;

class MyOpenRequests extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $builder = OfferingExecution::query()
            ->where(
                'user_id',
                Filament::auth()->id(),
            );

        return [
            Stat::make(
                'Open requests',
                (clone $builder)
                    ->whereIn('status', [
                        ExecutionStatus::Pending->value,
                        ExecutionStatus::Preparing->value,
                        ExecutionStatus::Running->value,
                    ])
                    ->count(),
            )
                ->icon('heroicon-o-clock'),

            Stat::make(
                'Completed',
                (clone $builder)
                    ->where(
                        'status',
                        ExecutionStatus::Completed->value,
                    )
                    ->count(),
            )
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(
                'Failed',
                (clone $builder)
                    ->where(
                        'status',
                        ExecutionStatus::Failed->value,
                    )
                    ->count(),
            )
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
