<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Staff\Pages;

use Filament\Pages\Page;
use Rimba\Work\Models\OfferingExecution;

class ViewWorkItem extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view =
        'bites::staff.pages.view-work-item';

    public OfferingExecution $execution;

    public function mount(
        string $execution,
    ): void {
        $this->execution = OfferingExecution::query()
            ->with([
                'offering.form',
                'offering.workflow.lifecycle',
                'subject',
                'workflowInstance',
                'formSubmission',
            ])
            ->findOrFail($execution);

        $this->authorize(
            'view',
            $this->execution,
        );
    }

    public function getTitle(): string
    {
        return $this->execution->offering->name;
    }

    public function getSubheading(): string
    {
        return 'Request #'
            .$this->execution->getKey();
    }
}
