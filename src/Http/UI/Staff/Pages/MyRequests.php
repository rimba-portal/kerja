<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Rimba\Work\Http\UI\Staff\Tables\MyRequestsTable;
use Rimba\Work\Models\OfferingExecution;

class MyRequests extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-document-text';

    protected static ?string $navigationLabel =
        'My Requests';

    protected static string|\UnitEnum|null $navigationGroup =
        'Work';

    protected static ?int $navigationSort = 30;

    protected string $view =
        'bites::staff.pages.my-requests';

    public function table(
        Table $table,
    ): Table {
        return MyRequestsTable::configure(
            table: $table,
            query: OfferingExecution::query()
                ->where(
                    'user_id',
                    Filament::auth()->id(),
                )
                ->with([
                    'offering',
                    'subject',
                    'workflowInstance',
                ]),
        );
    }
}
