<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Workflows\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Rimba\Work\Enums\WorkflowProviderType;
use Rimba\Work\Services\WorkAccessService;

class WorkflowForm
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema->components([
            Select::make('org_unit_id')
                ->label('Owning Unit')
                ->options(
                    fn (): array => app(WorkAccessService::class)
                        ->manageableUnits(Filament::auth()->user())
                        ->pluck('name', 'id')
                        ->all(),
                )
                ->required()
                ->searchable()
                ->preload(),
            Section::make('Workflow registration')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required(),

                    TextInput::make('code')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Textarea::make('description')
                        ->columnSpanFull(),

                    Select::make('provider')
                        ->options([
                            WorkflowProviderType::Packstub->value => 'Packstub',
                            WorkflowProviderType::Null->value => 'None',
                        ])
                        ->default(
                            WorkflowProviderType::Packstub->value,
                        )
                        ->required(),

                    TextInput::make('provider_key')
                        ->required(),

                    TextInput::make('provider_reference')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('version')
                        ->numeric()
                        ->default(1)
                        ->minValue(1),

                    Toggle::make('is_active')
                        ->default(true),
                ]),
        ]);
    }
}
