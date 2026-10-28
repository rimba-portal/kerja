<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Workflows;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Http\UI\Team\Resources\Workflows\Pages\CreateWorkflow;
use Rimba\Work\Http\UI\Team\Resources\Workflows\Pages\EditWorkflow;
use Rimba\Work\Http\UI\Team\Resources\Workflows\Pages\ListWorkflows;
use Rimba\Work\Http\UI\Team\Resources\Workflows\Pages\ViewWorkflow;
use Rimba\Work\Http\UI\Team\Resources\Workflows\Schemas\WorkflowForm;
use Rimba\Work\Http\UI\Team\Resources\Workflows\Schemas\WorkflowInfolist;
use Rimba\Work\Http\UI\Team\Resources\Workflows\Tables\WorkflowsTable;
use Rimba\Work\Models\Workflow;
use Rimba\Work\Services\WorkAccessService;

class WorkflowResource extends Resource
{
    protected static ?string $model = Workflow::class;

    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-share';

    protected static string|\UnitEnum|null $navigationGroup =
        'Work Design';

    protected static ?int $navigationSort = 30;

    public static function form(
        Schema $schema,
    ): Schema {
        return WorkflowForm::configure(
            $schema,
        );
    }

    public static function infolist(
        Schema $schema,
    ): Schema {
        return WorkflowInfolist::configure(
            $schema,
        );
    }

    public static function table(
        Table $table,
    ): Table {
        return WorkflowsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflows::route('/'),
            'create' => CreateWorkflow::route('/create'),
            'view' => ViewWorkflow::route('/{record}'),
            'edit' => EditWorkflow::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $unitIds = app(
            WorkAccessService::class,
        )->manageableUnitIds(
            Filament::auth()->user(),
        );

        return parent::getEloquentQuery()
            ->whereIn(
                'org_unit_id',
                $unitIds,
            );
    }
}
