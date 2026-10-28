<?php

declare(strict_types=1);

namespace Rimba\Work\Data;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Models\Offering;

final readonly class OfferingDefinition
{
    /**
     * @param  class-string<Model>|null  $subjectClass
     */
    public function __construct(
        public Offering $offering,
        public ?FormDefinition $form,
        public ?WorkflowDefinition $workflow,
        public ?string $subjectClass,
    ) {}
}
