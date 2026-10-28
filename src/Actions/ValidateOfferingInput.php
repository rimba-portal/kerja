<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Models\Offering;
use Rimba\Work\Services\FormManager;

class ValidateOfferingInput
{
    public function __construct(
        protected FormManager $forms,
    ) {}

    public function execute(
        Offering $offering,
        array $input,
        bool $alreadyValidated = false,
    ): array {
        if (
            ! $offering->requiresForm()
            || $alreadyValidated
        ) {
            return $input;
        }

        return $this->forms->validate(
            $offering->form,
            $input,
        );
    }
}
