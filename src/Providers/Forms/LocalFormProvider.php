<?php

declare(strict_types=1);

namespace Rimba\Work\Providers\Forms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Rimba\Work\Contracts\FormProvider;
use Rimba\Work\Data\FormDefinition;
use Rimba\Work\Enums\FormProviderType;
use Rimba\Work\Exceptions\FormProviderException;
use Rimba\Work\Models\Form;

class LocalFormProvider implements FormProvider
{
    public function name(): string
    {
        return FormProviderType::Local->value;
    }

    public function supports(Form $form): bool
    {
        return $form->provider === FormProviderType::Local;
    }

    public function exists(Form $form): bool
    {
        return is_array($form->schema)
            && $form->schema !== [];
    }

    public function resolve(Form $form): FormDefinition
    {
        if (! $this->exists($form)) {
            throw new FormProviderException(
                "Local form [{$form->code}] has no schema."
            );
        }

        return new FormDefinition(
            provider: $this->name(),
            key: $form->code,
            name: $form->name,
            definition: $form->schema ?? [],
        );
    }

    public function validate(
        Form $form,
        array $input,
    ): array {
        $rules = data_get(
            $form->schema,
            'validation',
            [],
        );

        return Validator::make(
            data: $input,
            rules: is_array($rules) ? $rules : [],
        )->validate();
    }

    public function findSubmission(
        int|string $id,
    ): ?Model {
        return null;
    }
}
