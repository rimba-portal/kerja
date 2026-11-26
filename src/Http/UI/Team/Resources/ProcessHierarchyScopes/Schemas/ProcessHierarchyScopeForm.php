<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\ProcessHierarchyScopes\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Floorplan\Models\Location;
use Rimba\Work\Models\Process;

final class ProcessHierarchyScopeForm
{
    public static function configure(Schema $schema): Schema
    {
        return self::schema($schema, includeProcess: true);
    }

    public static function configureForProcess(Schema $schema): Schema
    {
        return self::schema($schema, includeProcess: false);
    }

    private static function schema(Schema $schema, bool $includeProcess): Schema
    {
        $components = [];

        if ($includeProcess) {
            $components[] = Select::make('process_id')
                ->label('Process')
                ->relationship(name: 'process', titleAttribute: 'name')
                ->getOptionLabelFromRecordUsing(
                    fn (Process $record): string => trim("{$record->code} — {$record->name}"),
                )
                ->searchable(['code', 'name'])
                ->preload()
                ->required();
        }

        $components = [
            ...$components,
            Select::make('hierarchy_scope_id')
                ->label('Work Unit')
                ->relationship(
                    name: 'hierarchyScope',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query): Builder => $query
                        ->where('type', 'Work Unit')
                        ->orderBy('code'),
                )
                ->getOptionLabelFromRecordUsing(
                    fn (Location $record): string => trim("{$record->code} — {$record->name}"),
                )
                ->searchable(['code', 'name'])
                ->preload()
                ->required(),
            TextInput::make('scope_level')->label('Scope level')->default('Work Unit')->required()->maxLength(255),
            Textarea::make('description')->rows(3)->columnSpanFull(),
        ];

        return $schema->components([
            Section::make('Process scope')->schema($components)->columns(2),
            Section::make('Attributes')->schema([
                TextInput::make('attributes.source')->maxLength(255),
                KeyValue::make('attributes.metadata')
                    ->label('Metadata')
                    ->keyLabel('Property')
                    ->valueLabel('Value')
                    ->addActionLabel('Add property')
                    ->reorderable(),
            ])->collapsed(),
        ]);
    }
}
