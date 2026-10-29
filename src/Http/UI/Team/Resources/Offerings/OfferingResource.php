<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Offerings;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Rimba\Work\Http\UI\Team\Resources\Offerings\Schemas\OfferingForm;
use Rimba\Work\Http\UI\Team\Resources\Offerings\Schemas\OfferingInfolist;
use Rimba\Work\Http\UI\Team\Resources\Offerings\Tables\OfferingsTable;
use Rimba\Work\Models\Offering;
use Rimba\Work\Services\WorkAccessService;

class OfferingResource extends Resource
{
    protected static ?string $model = Offering::class;

    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup =
        'Work Design';

    protected static ?int $navigationSort = 40;

    public static function form(
        Schema $schema,
    ): Schema {
        return OfferingForm::configure(
            $schema,
        );
    }

    public static function infolist(
        Schema $schema,
    ): Schema {
        return OfferingInfolist::configure(
            $schema,
        );
    }

    public static function table(
        Table $table,
    ): Table {
        return OfferingsTable::configure(
            $table,
        );
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ExecutionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOfferings::route('/'),

            'create' => Pages\CreateOffering::route('/create'),

            'view' => Pages\ViewOffering::route('/{record}'),

            'edit' => Pages\EditOffering::route(
                '/{record}/edit'
            ),
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
