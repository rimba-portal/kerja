<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Checklists;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\Checklists\Pages\CreateChecklist;
use Rimba\Work\Http\UI\Admin\Resources\Checklists\Pages\EditChecklist;
use Rimba\Work\Http\UI\Admin\Resources\Checklists\Pages\ListChecklists;
use Rimba\Work\Http\UI\Admin\Resources\Checklists\Pages\ViewChecklist;
use Rimba\Work\Http\UI\Admin\Resources\Checklists\Schemas\ChecklistForm;
use Rimba\Work\Http\UI\Admin\Resources\Checklists\Schemas\ChecklistInfolist;
use Rimba\Work\Http\UI\Admin\Resources\Checklists\Tables\ChecklistsTable;
use Rimba\Work\Models\Checklist;

class ChecklistResource extends Resource
{
    protected static ?string $model = Checklist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ChecklistForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChecklistInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChecklistsTable::configure($table);
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
            'index' => ListChecklists::route('/'),
            'create' => CreateChecklist::route('/create'),
            'view' => ViewChecklist::route('/{record}'),
            'edit' => EditChecklist::route('/{record}/edit'),
        ];
    }
}
