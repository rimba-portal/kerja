<?php

declare(strict_types=1);

namespace Rimba\Work;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Rimba\Work\Http\UI\Staff\Pages\BrowseOfferings;
use Rimba\Work\Http\UI\Staff\Pages\ExecuteOffering;
use Rimba\Work\Http\UI\Staff\Pages\MyRequests;
use Rimba\Work\Http\UI\Staff\Pages\MyTasks;
use Rimba\Work\Http\UI\Staff\Pages\ViewWorkItem;
use Rimba\Work\Http\UI\Staff\Widgets\MyOpenRequests;
use Rimba\Work\Http\UI\Staff\Widgets\MyPendingTasks;
// use Rimba\Work\Http\UI\Team\Pages\TeamInbox;
// use Rimba\Work\Http\UI\Team\Pages\ViewTeamWorkItem;
use Rimba\Work\Http\UI\Team\Resources\Forms\FormResource;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\LifecycleResource;
use Rimba\Work\Http\UI\Team\Resources\OfferingExecutions\OfferingExecutionResource;
use Rimba\Work\Http\UI\Team\Resources\Offerings\OfferingResource;
use Rimba\Work\Http\UI\Team\Resources\Workflows\WorkflowResource;

class WorkPlugin implements Plugin
{
    protected bool $staffWorkspace = false;

    protected bool $teamWorkspace = false;

    protected bool $lifecycleManagement = false;

    protected bool $executionDiagnostics = false;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'rimba-work';
    }

    public function staffWorkspace(
        bool $condition = true,
    ): static {
        $this->staffWorkspace = $condition;

        return $this;
    }

    public function teamWorkspace(
        bool $condition = true,
    ): static {
        $this->teamWorkspace = $condition;

        return $this;
    }

    public function lifecycleManagement(
        bool $condition = true,
    ): static {
        $this->lifecycleManagement = $condition;

        return $this;
    }

    public function executionDiagnostics(
        bool $condition = true,
    ): static {
        $this->executionDiagnostics = $condition;

        return $this;
    }

    public function register(
        Panel $panel,
    ): void {
        if ($this->staffWorkspace) {
            $panel
                ->pages([
                    BrowseOfferings::class,
                    ExecuteOffering::class,
                    MyRequests::class,
                    MyTasks::class,
                    ViewWorkItem::class,
                ])
                ->widgets([
                    MyOpenRequests::class,
                    MyPendingTasks::class,
                ]);
        }

        if ($this->teamWorkspace) {
            $resources = [
                FormResource::class,
                WorkflowResource::class,
                OfferingResource::class,
            ];

            if ($this->lifecycleManagement) {
                $resources[] = LifecycleResource::class;
            }

            if ($this->executionDiagnostics) {
                $resources[] = OfferingExecutionResource::class;
            }

            $panel
                ->resources($resources)
                ->pages([
                    // TeamInbox::class,
                    // ViewTeamWorkItem::class,
                ]);
        }
    }

    public function boot(
        Panel $panel,
    ): void {}
}
