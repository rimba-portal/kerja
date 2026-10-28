<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Forms\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Rimba\Work\Enums\FormDisplayMode;
use Rimba\Work\Enums\FormProviderType;
use Rimba\Work\Services\WorkAccessService;

class FormForm
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema->components([
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
                ->preload(),
            Section::make('Form registration')
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
                        ->options(
                            collect(
                                FormProviderType::cases(),
                            )->mapWithKeys(
                                fn (
                                    FormProviderType $type,
                                ): array => [
                                    $type->value => str($type->value)
                                        ->headline()
                                        ->toString(),
                                ],
                            )->all(),
                        )
                        ->default(
                            FormProviderType::Packstub->value,
                        )
                        ->required(),

                    TextInput::make('provider_key')
                        ->label('Provider key')
                        ->required(),

                    TextInput::make('provider_reference')
                        ->label('Resolved provider ID')
                        ->disabled()
                        ->dehydrated(false),

                    Select::make('display_mode')
                        ->options(
                            collect(
                                FormDisplayMode::cases(),
                            )->mapWithKeys(
                                fn (
                                    FormDisplayMode $mode,
                                ): array => [
                                    $mode->value => str($mode->value)
                                        ->headline()
                                        ->toString(),
                                ],
                            )->all(),
                        )
                        ->default(
                            FormDisplayMode::Single->value,
                        )
                        ->required(),

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
