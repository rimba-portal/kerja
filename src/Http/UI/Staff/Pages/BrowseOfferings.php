<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Rimba\Work\Models\Offering;

class BrowseOfferings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel =
        'Request Work';

    protected static string|\UnitEnum|null $navigationGroup =
        'Work';

    protected static ?int $navigationSort = 10;

    protected string $view =
        'bites.staff.pages.browse-offerings';

    public string $search = '';

    public function getOfferingsProperty(): Collection
    {
        $user = Filament::auth()->user();

        return Offering::query()
            ->available()
            ->with([
                'form',
                'workflow.lifecycle',
            ])
            ->when(
                filled($this->search),
                fn ($query) => $query->where(
                    fn ($query) => $query
                        ->where(
                            'name',
                            'like',
                            "%{$this->search}%",
                        )
                        ->orWhere(
                            'description',
                            'like',
                            "%{$this->search}%",
                        ),
                ),
            )
            ->ordered()
            ->get()
            ->filter(
                fn (Offering $offering): bool => $user->can('execute', $offering),
            );
    }

    public function offeringUrl(
        Offering $offering,
    ): string {
        return ExecuteOffering::getUrl([
            'offering' => $offering->code,
        ]);
    }
}
