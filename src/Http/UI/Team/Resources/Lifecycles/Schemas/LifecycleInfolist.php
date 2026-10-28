<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Lifecycles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LifecycleInfolist
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema->components([
            Section::make('Lifecycle')
                ->columns(2)
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('code'),
                    TextEntry::make('category')
                        ->badge(),
                    TextEntry::make('parent.name')
                        ->placeholder('Root lifecycle'),
                    TextEntry::make('description')
                        ->columnSpanFull(),
                    TextEntry::make('is_active')
                        ->badge()
                        ->formatStateUsing(
                            fn (bool $state): string => $state
                                    ? 'Active'
                                    : 'Inactive',
                        ),
                ]),
        ]);
    }
}
