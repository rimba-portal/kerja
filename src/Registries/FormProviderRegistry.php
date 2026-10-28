<?php

declare(strict_types=1);

namespace Rimba\Work\Registries;

use Rimba\Work\Contracts\FormProvider;
use Rimba\Work\Exceptions\FormProviderException;
use Rimba\Work\Models\Form;

class FormProviderRegistry
{
    /**
     * @var array<string, FormProvider>
     */
    protected array $providers = [];

    public function register(
        FormProvider $provider,
    ): static {
        $this->providers[$provider->name()] = $provider;

        return $this;
    }

    public function get(string $name): FormProvider
    {
        return $this->providers[$name]
            ?? throw new FormProviderException(
                "Form provider [{$name}] is not registered."
            );
    }

    public function for(Form $form): FormProvider
    {
        $formProvider = $this->get(
            $form->provider->value,
        );

        if (! $formProvider->supports($form)) {
            throw new FormProviderException(
                "Provider [{$formProvider->name()}] does not support form [{$form->code}]."
            );
        }

        return $formProvider;
    }

    /**
     * @return array<string, FormProvider>
     */
    public function all(): array
    {
        return $this->providers;
    }
}
