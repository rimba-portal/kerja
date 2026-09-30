<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Admin\Resources\Artifacts;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Admin\Resources\Artifacts\Pages\ListArtifacts;
use Rimba\Work\Models\Artifact;
use UnitEnum;

class ArtifactResource extends Resource
{
    protected static ?string $model = Artifact::class;

    protected static string|UnitEnum|null $navigationGroup = 'Work';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-play';

    protected static ?int $navigationSort = 6;

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
            'index' => ListArtifacts::route('/'),
            // 'create' => \Rimba\Work\Http\UI\Admin\Resources\Artifacts\Pages\CreateArtifact::route('/create'),
            // 'view' => \Rimba\Work\Http\UI\Admin\Resources\Artifacts\Pages\ViewArtifact::route('/{record}'),
            // 'edit' => \Rimba\Work\Http\UI\Admin\Resources\Artifacts\Pages\EditArtifact::route('/{record}/edit'),
            //
        ];
    }
}
