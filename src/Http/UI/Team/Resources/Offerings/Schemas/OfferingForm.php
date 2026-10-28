<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Offerings\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Rimba\Work\Contracts\SubjectRegistry;
use Rimba\Work\Enums\OfferingAvailability;
use Rimba\Work\Enums\OfferingVisibility;
use Rimba\Work\Enums\SubjectCreationMode;
use Rimba\Work\Models\Form;
use Rimba\Work\Models\Workflow;
use Rimba\Work\Services\WorkAccessService;

class OfferingForm
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema->components([
            Tabs::make('Offering')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('General')
                        ->schema([
                            Section::make()
                                ->columns(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->required(),

                                    TextInput::make('code')
                                        ->required()
                                        ->unique(
                                            ignoreRecord: true,
                                        ),
                                    Select::make('org_unit_id')
                                        ->label('Owning Unit')
                                        ->options(
                                            fn (): array => app(
                                                WorkAccessService::class,
                                            )
                                                ->manageableUnits(
                                                    Filament::auth()->user(),
                                                )
                                                ->pluck('name', 'id')
                                                ->all(),
                                        )
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->live(),
                                    Textarea::make('description')
                                        ->columnSpanFull(),

                                    TextInput::make('icon'),

                                    Select::make('color')
                                        ->options([
                                            'primary' => 'Primary',
                                            'success' => 'Success',
                                            'warning' => 'Warning',
                                            'danger' => 'Danger',
                                            'info' => 'Info',
                                            'gray' => 'Gray',
                                        ]),

                                    TextInput::make('sort')
                                        ->numeric()
                                        ->default(0),

                                    Toggle::make('is_active')
                                        ->default(true),
                                ]),
                        ]),

                    Tab::make('Request')
                        ->schema([
                            Select::make('form_code')
                                ->label('Request form')
                                ->options(
                                    fn (Get $get): array => Form::query()
                                        ->where(
                                            'org_unit_id',
                                            $get('org_unit_id'),
                                        )
                                        ->active()
                                        ->orderBy('name')
                                        ->pluck(
                                            'name',
                                            'code',
                                        )
                                        ->all(),
                                )
                                ->searchable()
                                ->preload(),

                            Select::make('workflow_code')
                                ->label('Workflow')
                                ->options(
                                    fn (Get $get): array => Workflow::query()
                                        ->where(
                                            'org_unit_id',
                                            $get('org_unit_id'),
                                        )
                                        ->active()
                                        ->orderBy('name')
                                        ->pluck(
                                            'name',
                                            'code',
                                        )
                                        ->all(),
                                )
                                ->searchable()
                                ->preload(),
                        ]),

                    Tab::make('Subject Mapping')
                        ->schema([
                            Select::make('subject_type')
                                ->options(
                                    fn (): array => collect(
                                        app(
                                            SubjectRegistry::class,
                                        )->all(),
                                    )
                                        ->keys()
                                        ->mapWithKeys(
                                            fn (
                                                string $alias,
                                            ): array => [
                                                $alias => str($alias)
                                                    ->replace(
                                                        '.',
                                                        ' ',
                                                    )
                                                    ->headline()
                                                    ->toString(),
                                            ],
                                        )
                                        ->all(),
                                )
                                ->searchable(),

                            Select::make(
                                'subject_creation_mode'
                            )
                                ->options(
                                    collect(
                                        SubjectCreationMode::cases(),
                                    )->mapWithKeys(
                                        fn (
                                            SubjectCreationMode $mode,
                                        ): array => [
                                            $mode->value => str($mode->value)
                                                ->headline()
                                                ->toString(),
                                        ],
                                    )->all(),
                                )
                                ->default(
                                    SubjectCreationMode::Create->value,
                                ),

                            KeyValue::make('input_mapping')
                                ->keyLabel('Form field')
                                ->valueLabel(
                                    'Subject attribute'
                                ),

                            KeyValue::make('context_mapping')
                                ->keyLabel('Context path')
                                ->valueLabel(
                                    'Subject attribute'
                                ),

                            KeyValue::make('defaults')
                                ->keyLabel(
                                    'Subject attribute'
                                )
                                ->valueLabel('Default value'),
                        ]),

                    Tab::make('Availability')
                        ->schema([
                            Select::make('visibility')
                                ->options(
                                    collect(
                                        OfferingVisibility::cases(),
                                    )->mapWithKeys(
                                        fn (
                                            OfferingVisibility $value,
                                        ): array => [
                                            $value->value => str($value->value)
                                                ->headline()
                                                ->toString(),
                                        ],
                                    )->all(),
                                )
                                ->default(
                                    OfferingVisibility::Team->value,
                                ),

                            Select::make('availability')
                                ->options(
                                    collect(
                                        OfferingAvailability::cases(),
                                    )->mapWithKeys(
                                        fn (
                                            OfferingAvailability $value,
                                        ): array => [
                                            $value->value => str($value->value)
                                                ->headline()
                                                ->toString(),
                                        ],
                                    )->all(),
                                )
                                ->default(
                                    OfferingAvailability::Available->value,
                                ),
                        ]),
                ]),
        ]);
    }
}
