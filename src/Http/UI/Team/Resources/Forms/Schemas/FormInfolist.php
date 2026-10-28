<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Forms\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormInfolist
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema->components([
            Section::make('Form registration')
                ->columns(2)
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('code'),
                    TextEntry::make('provider')
                        ->badge(),
                    TextEntry::make('provider_key'),
                    TextEntry::make('display_mode')
                        ->badge(),
                    TextEntry::make('offerings_count')
                        ->counts('offerings'),
                    TextEntry::make('description')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
