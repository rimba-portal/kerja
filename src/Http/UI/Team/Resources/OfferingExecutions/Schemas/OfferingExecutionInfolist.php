<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\OfferingExecutions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class OfferingExecutionInfolist
{
    public static function configure(
        Schema $schema,
    ): Schema {
        return $schema->components([
            Section::make('Execution')
                ->columns(2)
                ->schema([
                    TextEntry::make('offering.name')
                        ->label('Offering'),

                    TextEntry::make('status')
                        ->badge(),

                    TextEntry::make('staff.name')
                        ->label('Requested by')
                        ->placeholder('Unknown'),

                    TextEntry::make('orgUnit.name')
                        ->label('Organizational Unit')
                        ->placeholder('None'),

                    TextEntry::make('created_at')
                        ->label('Created')
                        ->dateTime(),

                    TextEntry::make('started_at')
                        ->label('Started')
                        ->dateTime()
                        ->placeholder('Not started'),

                    TextEntry::make('completed_at')
                        ->label('Completed')
                        ->dateTime()
                        ->placeholder('Not completed'),

                    TextEntry::make('failed_at')
                        ->label('Failed')
                        ->dateTime()
                        ->placeholder('No'),
                ]),

            Section::make('Failure')
                ->schema([
                    TextEntry::make('failure_code')
                        ->label('Code')
                        ->placeholder('None'),

                    TextEntry::make('failure_message')
                        ->label('Message')
                        ->placeholder('None'),
                ])
                ->visible(
                    fn ($record): bool => filled($record?->failure_message),
                ),
        ]);
    }
}
