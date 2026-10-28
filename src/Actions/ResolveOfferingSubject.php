<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Contracts\SubjectRegistry;
use Rimba\Work\Models\Offering;

class ResolveOfferingSubject
{
    public function __construct(
        protected SubjectRegistry $subjects,
    ) {}

    /**
     * @return class-string<Model>|null
     */
    public function execute(
        Offering $offering,
    ): ?string {
        if (! $offering->requiresSubject()) {
            return null;
        }

        return $this->subjects->resolve(
            $offering->subject_type,
        );
    }
}
