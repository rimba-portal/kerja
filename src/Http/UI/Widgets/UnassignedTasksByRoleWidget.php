<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

final class UnassignedTasksByRoleWidget extends StatsOverviewWidget
{
    protected ?string $heading =
        'Unassigned Work By Role';

    protected function getStats(): array
    {
        $stats = [];

        $total = DB::table('work_tasks')
            ->whereNull('assignee_id')
            ->whereIn('status', [
                'ready',
                'assigned',
                'started',
            ])
            ->count();

        $stats[] = Stat::make(
            'Total Unassigned',
            number_format($total)
        )
            ->color($total > 0 ? 'danger' : 'success')
            ->description('Awaiting assignment')
            ->icon('heroicon-o-exclamation-triangle');

        $roles = DB::table('work_tasks')
            ->join(
                'work_packages',
                'work_tasks.work_package_id',
                '=',
                'work_packages.id'
            )
            ->join(
                'job_roles',
                'work_packages.actor_job_role_id',
                '=',
                'job_roles.id'
            )
            ->whereNull('work_tasks.assignee_id')
            ->where('work_tasks.status', 'ready')
            ->select([
                'job_roles.name',
                DB::raw('count(*) as total'),
            ])
            ->groupBy('job_roles.name')
            ->orderByDesc('total')
            ->get();

        foreach ($roles as $role) {
            $stats[] = Stat::make(
                $role->name,
                number_format($role->total)
            )
                ->description('Ready tasks')
                ->color('warning')
                ->icon('heroicon-o-user-group');
        }

        return $stats;
    }
}
