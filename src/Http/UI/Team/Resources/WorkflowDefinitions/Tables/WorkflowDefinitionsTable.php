<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkflowDefinitions\Tables;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Rimba\Work\Models\WorkflowDefinition;
use Rimba\Work\Services\WorkflowDefinitionRepository;
use Rimba\Work\Services\WorkflowDefinitionValidator;

class WorkflowDefinitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->records(
                fn (): Collection => app(WorkflowDefinitionRepository::class)
                    ->all()
                    ->map(
                        fn (array $definition): WorkflowDefinition => WorkflowDefinition::fromDefinition($definition)
                    )
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Workflow')
                    ->description(
                        fn (WorkflowDefinition $record): string => (string) $record->slug
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('version')
                    ->prefix('v')
                    ->sortable(),

                TextColumn::make('first_workpackage')
                    ->label('First WorkPackage')
                    ->badge()
                    ->searchable(),

                TextColumn::make('workpackages')
                    ->label('WorkPackages')
                    ->state(
                        fn (WorkflowDefinition $record): int => count($record->workpackages ?? [])
                    )
                    ->badge()
                    ->alignCenter(),
            ])
            ->recordActions([
                Action::make('validate')
                    ->label('Validate')
                    ->icon('heroicon-o-check-badge')
                    ->color('gray')
                    ->action(
                        function (WorkflowDefinition $record): void {
                            $errors = app(WorkflowDefinitionValidator::class)
                                ->validate($record->getAttributes());

                            if ($errors === []) {
                                Notification::make()
                                    ->title('Workflow is valid')
                                    ->body('The SIPOC workflow definition is valid.')
                                    ->success()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Workflow is invalid')
                                ->body(implode(PHP_EOL, $errors))
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    ),
            ])
            ->emptyStateHeading('No workflow definitions found')
            ->emptyStateDescription(
                'Add a workflow JSON definition to the configured SIPOC setup directory.'
            )
            ->emptyStateIcon('heroicon-o-document-text')
            ->defaultSort('title');
    }
}
