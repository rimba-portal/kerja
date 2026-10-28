<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Rimba\Work\Models\Form;

interface FormRenderer
{
    public function component(
        Form $form,
        array $values = [],
    ): string;
}
