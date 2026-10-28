<?php

declare(strict_types=1);

namespace Rimba\Work\Actions;

use Rimba\Work\Contracts\OfferingAuthorizer;
use Rimba\Work\Contracts\OfferingContextBuilder;
use Rimba\Work\Data\PreparedOffering;
use Rimba\Work\Enums\OfferingAvailability;
use Rimba\Work\Exceptions\OfferingUnavailableException;
use Rimba\Work\Models\Offering;

class PrepareOffering
{
    public function __construct(
        protected ResolveOfferingForm $resolveForm,
        protected ResolveOfferingWorkflow $resolveWorkflow,
        protected ResolveOfferingSubject $resolveSubject,
        protected OfferingContextBuilder $contextBuilder,
        protected OfferingAuthorizer $authorizer,
    ) {}

    public function execute(
        Offering $offering,
        array $metadata = [],
    ): PreparedOffering {
        $this->authorizer->authorize(
            offering: $offering,
            user: auth()->user(),
        );

        if (! $offering->is_active) {
            throw new OfferingUnavailableException(
                "Offering [{$offering->code}] is inactive."
            );
        }

        if (
            $offering->availability
            !== OfferingAvailability::Available
        ) {
            throw new OfferingUnavailableException(
                "Offering [{$offering->code}] is unavailable."
            );
        }

        if (
            $offering->requiresForm()
            && ! $offering->form?->is_active
        ) {
            throw new OfferingUnavailableException(
                "Form for offering [{$offering->code}] is inactive or missing."
            );
        }

        if (
            $offering->requiresWorkflow()
            && ! $offering->workflow?->is_active
        ) {
            throw new OfferingUnavailableException(
                "Workflow for offering [{$offering->code}] is inactive or missing."
            );
        }

        return new PreparedOffering(
            offering: $offering,
            form: $this->resolveForm->execute($offering),
            workflow: $this->resolveWorkflow->execute($offering),
            subjectClass: $this->resolveSubject->execute($offering),
            context: $this->contextBuilder->build(
                offering: $offering,
                metadata: $metadata,
            ),
        );
    }
}
