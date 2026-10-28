<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Rimba\Work\Models\Offering;

class ExecuteOffering extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view =
        'bites::staff.pages.execute-offering';

    public Offering $offering;

    public function mount(
        string $offering,
    ): void {
        $this->offering = Offering::query()
            ->with([
                'form',
                'workflow.lifecycle',
            ])
            ->where('code', $offering)
            ->firstOrFail();

        $this->authorize(
            'execute',
            $this->offering,
        );

        abort_unless(
            $this->offering->requiresForm(),
            404,
        );
    }

    public function getTitle(): string
    {
        return $this->offering->name;
    }

    public function getSubheading(): ?string
    {
        return $this->offering->description;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to offerings')
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url(
                    BrowseOfferings::getUrl(),
                ),
        ];
    }
}
