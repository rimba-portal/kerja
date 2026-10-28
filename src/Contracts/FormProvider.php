<?php

declare(strict_types=1);

namespace Rimba\Work\Contracts;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Data\FormDefinition;
use Rimba\Work\Models\Form;

interface FormProvider
{
    public function name(): string;

    public function supports(Form $form): bool;

    public function exists(Form $form): bool;

    public function resolve(Form $form): FormDefinition;

    public function validate(
        Form $form,
        array $input,
    ): array;

    public function findSubmission(
        int|string $id,
    ): ?Model;
}
