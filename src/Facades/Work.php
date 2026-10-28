<?php

declare(strict_types=1);

namespace Rimba\Work\Facades;

use Illuminate\Support\Facades\Facade;
use Rimba\Work\Services\WorkManager;

/**
 * @method static \Rimba\Work\Services\OfferingManager offerings()
 * @method static \Rimba\Work\Services\FormManager forms()
 * @method static \Rimba\Work\Services\WorkflowManager workflows()
 * @method static \Rimba\Work\Contracts\SubjectRegistry subjects()
 * @method static \Rimba\Work\Data\ExecutionResult execute(
 *     \Rimba\Work\Models\Offering|string $offering,
 *     array $input = [],
 *     array $metadata = [],
 *     ?\Illuminate\Database\Eloquent\Model $subject = null,
 *     ?\Illuminate\Database\Eloquent\Model $formSubmission = null,
 *     bool $alreadyValidated = false,
 *     ?bool $queue = null,
 * )
 */
class Work extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WorkManager::class;
    }
}
