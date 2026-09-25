<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\ExecutionType;

class ActorResolverService
{
    public function resolve(
        array $workPackage,
        ?Model $initiator = null,
        array $context = []
    ): ?Model {
        if (
            strtolower(
                (string) ($workPackage['execution_type'] ?? '')
            ) !== ExecutionType::Human->value
        ) {
            return null;
        }

        $selector = $workPackage['actor_selector'] ?? 'initiator';

        if ($selector === 'initiator') {
            return $initiator;
        }

        $candidate = data_get($context, $selector);

        return $candidate instanceof Model
            ? $candidate
            : null;
    }
}
