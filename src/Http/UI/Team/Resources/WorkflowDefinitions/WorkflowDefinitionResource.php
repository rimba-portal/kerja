<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkflowDefinitions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\WorkflowDefinitions\Pages\ListWorkflowDefinitions;
use Rimba\Work\Http\UI\Team\Resources\WorkflowDefinitions\Schemas\WorkflowDefinitionForm;
use Rimba\Work\Http\UI\Team\Resources\WorkflowDefinitions\Tables\WorkflowDefinitionsTable;
use Rimba\Work\Models\WorkflowDefinition;
use UnitEnum;

class WorkflowDefinitionResource extends Resource
{
    protected static ?string $model = WorkflowDefinition::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup =
        'Workflow';

    protected static ?string $navigationLabel =
        'SIPOC Workflow Studio';

    protected static ?string $modelLabel =
        'Workflow Definition';

    protected static ?string $pluralModelLabel =
        'Workflow Definitions';

    protected static ?string $slug =
        'sipoc/workflow-definitions';

    public static function form(Schema $schema): Schema
    {
        return WorkflowDefinitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowDefinitionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowDefinitions::route('/'),
        ];
    }
}
