<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Rimba\Work\Services\WorkflowDefinitionRepository;
use UnitEnum;

class WorkflowDefinition extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'slug';

    protected $keyType = 'string';

    protected $guarded = [];

    public static function fromDefinition(array $definition): self
    {
        $model = new self();
        $model->setRawAttributes($definition, true);
        $model->exists = true;

        return $model;
    }

    public function save(array $options = []): bool
    {
        throw new \LogicException(
            'Use WorkflowDefinitionRepository.'
        );
    }
}

class WorkflowStudioResource extends Resource
{
    protected static ?string $model = WorkflowDefinition::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup =
        'Workflow';

    protected static ?string $navigationLabel =
        'SIPOC Workflow Studio';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Workflow')
                    ->description(
                        fn (WorkflowDefinition $record): string =>
                            (string) $record->slug
                    )
                    ->searchable(),

                TextColumn::make('version')
                    ->prefix('v'),

                TextColumn::make('first_workpackage')
                    ->label('First WorkPackage'),

                TextColumn::make('workpackages')
                    ->label('WorkPackages')
                    ->state(
                        fn (WorkflowDefinition $record): int =>
                            count($record->workpackages ?? [])
                    )
                    ->badge(),
            ])
            ->recordActions([
                Action::make('validate')
                    ->icon('heroicon-o-check-badge')
                    ->action(
                        function (
                            WorkflowDefinition $record
                        ): void {
                            $errors = app(
                                \Rimba\Work\Services
                                \WorkflowDefinitionValidator::class
                            )->validate($record->getAttributes());

                            Notification::make()
                                ->title(
                                    $errors === []
                                        ? 'Workflow is valid'
                                        : 'Workflow is invalid'
                                )
                                ->body(
                                    $errors === []
                                        ? 'The SIPOC definition is valid.'
                                        : implode(PHP_EOL, $errors)
                                )
                                ->color(
                                    $errors === []
                                        ? 'success'
                                        : 'danger'
                                )
                                ->send();
                        }
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWorkflowStudio::route('/'),
        ];
    }
}

class ManageWorkflowStudio extends ListRecords
{
    protected static string $resource =
        WorkflowStudioResource::class;

    protected static ?string $title =
        'SIPOC Workflow Studio';

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->records(
                fn (): Collection =>
                    app(WorkflowDefinitionRepository::class)
                        ->all()
                        ->map(
                            fn (array $definition):
                                WorkflowDefinition =>
                                    WorkflowDefinition::fromDefinition(
                                        $definition
                                    )
                        )
            );
    }
}