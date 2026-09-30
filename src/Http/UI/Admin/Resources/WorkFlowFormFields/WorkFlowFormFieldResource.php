<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\WorkFlowFormFields;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\WorkFlowFormFields\Pages\ListWorkFlowFormFields;
use Rimba\Work\Models\WorkFlowFormField;
use UnitEnum;

class WorkFlowFormFieldResource extends Resource
{
    protected static ?string $model = WorkFlowFormField::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 12;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkFlowFormFields::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowFormFields\Pages\CreateWorkFlowFormField::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowFormFields\Pages\ViewWorkFlowFormField::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\WorkFlowFormFields\Pages\EditWorkFlowFormField::route('/{record}/edit'),
            //
        ];
    }
}
