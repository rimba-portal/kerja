<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\Lifecycles;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\Pages\CreateLifecycle;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\Pages\EditLifecycle;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\Pages\ListLifecycles;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\Pages\ViewLifecycle;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\Schemas\LifecycleForm;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\Schemas\LifecycleInfolist;
use Rimba\Work\Http\UI\Team\Resources\Lifecycles\Tables\LifecyclesTable;
use Rimba\Work\Models\Lifecycle;

class LifecycleResource extends Resource
{
    protected static ?string $model = Lifecycle::class;

    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-arrow-path-rounded-square';

    protected static string|\UnitEnum|null $navigationGroup =
        'Work Design';

    protected static ?int $navigationSort = 10;

    public static function form(
        Schema $schema,
    ): Schema {
        return LifecycleForm::configure($schema);
    }

    public static function infolist(
        Schema $schema,
    ): Schema {
        return LifecycleInfolist::configure($schema);
    }

    public static function table(
        Table $table,
    ): Table {
        return LifecyclesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLifecycles::route('/'),
            'create' => CreateLifecycle::route('/create'),
            'view' => ViewLifecycle::route('/{record}'),
            'edit' => EditLifecycle::route('/{record}/edit'),
        ];
    }
}
