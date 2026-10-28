<?php

declare(strict_types=1);

namespace Rimba\Work\Providers\Forms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Contracts\FormProvider;
use Rimba\Work\Data\FormDefinition;
use Rimba\Work\Enums\FormProviderType;
use Rimba\Work\Exceptions\FormProviderException;
use Rimba\Work\Models\Form;

class PackstubFormProvider implements FormProvider
{
    public function name(): string
    {
        return FormProviderType::Packstub->value;
    }

    public function supports(Form $form): bool
    {
        return $form->provider === FormProviderType::Packstub;
    }

    public function exists(Form $form): bool
    {
        return $this->query($form)->exists();
    }

    public function resolve(Form $form): FormDefinition
    {
        $providerForm = $this->query($form)->first();

        if (! $providerForm) {
            throw new FormProviderException(
                "Packstub form [{$form->provider_key}] was not found."
            );
        }

        $definitionColumn = config(
            'work.providers.forms.drivers.packstub.definition_column',
            'definition',
        );

        return new FormDefinition(
            provider: $this->name(),
            key: (string) $form->provider_key,
            name: (string) (
                data_get($providerForm, 'name')
                ?? $form->name
            ),
            definition: (array) data_get(
                $providerForm,
                $definitionColumn,
                [],
            ),
            providerModel: $providerForm,
        );
    }

    public function validate(
        Form $form,
        array $input,
    ): array {
        $validatorClass = config(
            'work.providers.forms.drivers.packstub.validator',
        );

        /*
         * The normal path is:
         *
         * Packstub renders and validates the form, stores its submission,
         * then passes validated values to RIMBA with alreadyValidated=true.
         */
        if (! is_string($validatorClass)) {
            return $input;
        }

        $validator = app($validatorClass);

        if (! method_exists($validator, 'validate')) {
            throw new FormProviderException(
                "Packstub validator [{$validatorClass}] must provide validate()."
            );
        }

        return $validator->validate(
            form: $this->resolve($form)->providerModel,
            input: $input,
        );
    }

    public function findSubmission(
        int|string $id,
    ): ?Model {
        $modelClass = $this->submissionModelClass();

        return $modelClass::query()->find($id);
    }

    protected function query(Form $form): Builder
    {
        $modelClass = $this->formModelClass();

        $lookupColumn = config(
            'work.providers.forms.drivers.packstub.lookup_column',
            'slug',
        );

        return $modelClass::query()->where(
            $lookupColumn,
            $form->provider_key,
        );
    }

    /**
     * @return class-string<Model>
     */
    protected function formModelClass(): string
    {
        $modelClass = config(
            'work.providers.forms.drivers.packstub.form_model',
        );

        return $this->assertModel(
            modelClass: $modelClass,
            type: 'form',
        );
    }

    /**
     * @return class-string<Model>
     */
    protected function submissionModelClass(): string
    {
        $modelClass = config(
            'work.providers.forms.drivers.packstub.submission_model',
        );

        return $this->assertModel(
            modelClass: $modelClass,
            type: 'submission',
        );
    }

    /**
     * @return class-string<Model>
     */
    protected function assertModel(
        mixed $modelClass,
        string $type,
    ): string {
        if (
            ! is_string($modelClass)
            || ! is_a($modelClass, Model::class, true)
        ) {
            throw new FormProviderException(
                "The Packstub {$type} model is not configured correctly."
            );
        }

        return $modelClass;
    }
}
