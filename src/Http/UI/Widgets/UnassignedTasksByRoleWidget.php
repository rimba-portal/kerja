<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Models\TaskInstance;

class UnassignedTasksByRoleWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Unassigned Tasks By Role';

    protected function getStats(): array
    {
        $stats = [];

        $totalUnassigned = TaskInstance::query()
            ->whereNull('assigned_to_id')
            ->where('is_completed', false)
            ->count();

        $stats[] = Stat::make('Total Unassigned', number_format($totalUnassigned))
            ->description('Pending tasks without assigned staff')
            ->color($totalUnassigned > 0 ? 'danger' : 'success')
            ->icon('heroicon-o-exclamation-triangle');

        $counts = TaskInstance::query()
            ->join('tasks', 'task_instances.task_id', '=', 'tasks.id')
            ->join('roles', 'tasks.role_id', '=', 'roles.id')
            ->whereNull('task_instances.assigned_to_id')
            ->where('task_instances.is_completed', false)
            ->select([
                'roles.id as role_id',
                'roles.name as role_name',
                DB::raw('COUNT(task_instances.id) as total'),
            ])
            ->groupBy('roles.id', 'roles.name')
            ->orderByDesc('total')
            ->get();

        foreach ($counts as $count) {
            $stats[] = Stat::make(
                str($count->role_name)->headline()->toString(),
                number_format((int) $count->total),
            )
                ->description('Unassigned pending tasks')
                ->color('warning')
                ->icon('heroicon-o-user-group');
        }

        return $stats;
    }
}
