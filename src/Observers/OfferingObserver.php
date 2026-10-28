<?php

declare(strict_types=1);

namespace Rimba\Work\Observers;

use Rimba\Work\Models\Offering;

class OfferingObserver
{
    public function saving(
        Offering $offering,
    ): void {
        if (! $offering->requiresForm()) {
            $offering->form_code = null;
        }

        if (! $offering->requiresWorkflow()) {
            $offering->workflow_code = null;
        }

        if (! $offering->requiresSubject()) {
            $offering->subject_type = null;
        }
    }
}
