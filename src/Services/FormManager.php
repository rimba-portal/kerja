<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Rimba\Work\Contracts\FormProvider;
use Rimba\Work\Data\FormDefinition;
use Rimba\Work\Models\Form;
use Rimba\Work\Registries\FormProviderRegistry;

class FormManager
{
    public function __construct(
        protected FormProviderRegistry $providers,
    ) {}

    public function provider(
        Form $form,
    ): FormProvider {
        return $this->providers->for($form);
    }

    public function resolve(
        Form $form,
    ): FormDefinition {
        return $this
            ->provider($form)
            ->resolve($form);
    }

    public function validate(
        Form $form,
        array $input,
    ): array {
        return $this
            ->provider($form)
            ->validate($form, $input);
    }
}
