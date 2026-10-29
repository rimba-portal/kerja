<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Forms;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Http\UI\Team\Resources\Forms\Pages\CreateForm;
use Rimba\Work\Http\UI\Team\Resources\Forms\Pages\EditForm;
use Rimba\Work\Http\UI\Team\Resources\Forms\Pages\ListForms;
use Rimba\Work\Http\UI\Team\Resources\Forms\Pages\ViewForm;
use Rimba\Work\Http\UI\Team\Resources\Forms\Schemas\FormForm;
use Rimba\Work\Http\UI\Team\Resources\Tasks\Schemas\FormInfolist;
use Rimba\Work\Http\UI\Team\Resources\Tasks\Tables\FormsTable;
use Rimba\Work\Models\Form;
use Rimba\Work\Services\WorkAccessService;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup =
        'Work Design';

    protected static ?int $navigationSort = 20;

    public static function form(
        Schema $schema,
    ): Schema {
        return FormForm::configure($schema);
    }

    public static function infolist(
        Schema $schema,
    ): Schema {
        return FormInfolist::configure($schema);
    }

    public static function table(
        Table $table,
    ): Table {
        return FormsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'view' => ViewForm::route('/{record}'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $builder = parent::getEloquentQuery();
        $user = Filament::auth()->user();
        if ($user === null) {
            return $builder->whereRaw('1 = 0');
        }

        $unitIds = app(WorkAccessService::class)->manageableUnitIds($user);
        if ($unitIds === []) {
            return $builder->whereRaw('1 = 0');
        }

        return $builder->whereIn('org_unit_id', $unitIds);
    }
}
