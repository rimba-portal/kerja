<?php

declare(strict_types=1);

namespace Rimba\Work\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Rimba\Work\Models\Offering;
use Rimba\Work\Services\FormManager;

class DynamicOfferingForm extends Component
{
    public Offering $offering;

    public mixed $providerForm = null;

    public array $values = [];

    public bool $preview = false;

    public function mount(
        Offering $offering,
        FormManager $forms,
        array $values = [],
        bool $preview = false,
    ): void {
        $this->offering = $offering;
        $this->values = $values;
        $this->preview = $preview;

        $formDefinition = $forms->resolve(
            $offering->form,
        );

        $this->providerForm =
            $formDefinition->providerModel
            ?? $formDefinition->key;
    }

    public function render(): View
    {
        return view(
            'bites::livewire.dynamic-offering-form',
        );
    }
}
