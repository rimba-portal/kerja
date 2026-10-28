<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Data\FormDefinition;
use Rimba\Work\Models\Offering;
use Rimba\Work\Services\FormManager;

class ResolveOfferingForm
{
    public function __construct(
        protected FormManager $forms,
    ) {}

    public function execute(
        Offering $offering,
    ): ?FormDefinition {
        if (! $offering->requiresForm()) {
            return null;
        }

        return $this->forms->resolve(
            $offering->form,
        );
    }
}
