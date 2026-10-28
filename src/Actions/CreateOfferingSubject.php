<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Data\MappedOfferingData;
use Rimba\Work\Enums\SubjectCreationMode;
use Rimba\Work\Exceptions\OfferingExecutionException;
use Rimba\Work\Models\Offering;

class CreateOfferingSubject
{
    /**
     * @param  class-string<Model>|null  $subjectClass
     */
    public function execute(
        Offering $offering,
        ?string $subjectClass,
        MappedOfferingData $mapped,
        ?Model $existingSubject = null,
    ): ?Model {
        return match ($offering->subject_creation_mode) {
            SubjectCreationMode::None => null,

            SubjectCreationMode::Create => $this->create(
                subjectClass: $subjectClass,
                attributes: $mapped->attributes,
            ),

            SubjectCreationMode::Select => $this->select(
                existingSubject: $existingSubject,
            ),

            SubjectCreationMode::Update => $this->update(
                existingSubject: $existingSubject,
                attributes: $mapped->attributes,
            ),
        };
    }

    /**
     * @param  class-string<Model>|null  $subjectClass
     */
    protected function create(
        ?string $subjectClass,
        array $attributes,
    ): Model {
        if (! $subjectClass) {
            throw new OfferingExecutionException(
                'A subject class is required for create mode.'
            );
        }

        /** @var Model $subject */
        $subject = new $subjectClass;

        $subject->fill($attributes);
        $subject->save();

        return $subject;
    }

    protected function select(
        ?Model $existingSubject,
    ): Model {
        if (! $existingSubject instanceof Model) {
            throw new OfferingExecutionException(
                'An existing subject is required for select mode.'
            );
        }

        return $existingSubject;
    }

    protected function update(
        ?Model $existingSubject,
        array $attributes,
    ): Model {
        if (! $existingSubject instanceof Model) {
            throw new OfferingExecutionException(
                'An existing subject is required for update mode.'
            );
        }

        $existingSubject->fill($attributes);
        $existingSubject->save();

        return $existingSubject->refresh();
    }
}
