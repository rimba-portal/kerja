<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Lifecycles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Rimba\Work\Enums\LifecycleCategory;
use Rimba\Work\Models\Lifecycle;

class LifecycleForm
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema->components([
            Section::make('Lifecycle')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('code')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Textarea::make('description')
                        ->columnSpanFull(),

                    Select::make('category')
                        ->options(
                            collect(
                                LifecycleCategory::cases(),
                            )->mapWithKeys(
                                fn (
                                    LifecycleCategory $category,
                                ): array => [
                                    $category->value => str($category->value)
                                        ->headline()
                                        ->toString(),
                                ],
                            )->all(),
                        )
                        ->required(),

                    Select::make('parent_code')
                        ->label('Parent lifecycle')
                        ->options(
                            fn (): array => Lifecycle::query()
                                ->ordered()
                                ->pluck(
                                    'name',
                                    'code',
                                )
                                ->all(),
                        )
                        ->searchable()
                        ->preload(),

                    TextInput::make('sort')
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_active')
                        ->default(true),
                ]),
        ]);
    }
}
