<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Rimba\Work\Contracts\WorkflowTaskProvider;
use Rimba\Work\Http\UI\Staff\Tables\MyTasksTable;

class MyTasks extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel =
        'My Tasks';

    protected static string|\UnitEnum|null $navigationGroup =
        'Work';

    protected static ?int $navigationSort = 20;

    protected string $view =
        'work::filament.staff.pages.my-tasks';

    public function table(
        Table $table,
    ): Table {
        $workflowTaskProvider = app(
            WorkflowTaskProvider::class,
        );

        return MyTasksTable::configure(
            table: $table,
            query: $workflowTaskProvider->assignedQuery(
                Filament::auth()->user(),
            ),
            provider: $workflowTaskProvider,
        );
    }
}
