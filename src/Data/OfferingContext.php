<?php

declare(strict_types=1);

namespace Rimba\Work\Data;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class OfferingContext implements Arrayable
{
    public function __construct(
        public ?string $userId = null,
        public ?string $staffId = null,
        public ?string $jobPositionId = null,
        public ?string $orgTeamId = null,
        public ?string $orgUnitId = null,
        public ?string $offeringCode = null,
        public string $source = 'filament',
        public array $metadata = [],
    ) {}

    public function toArray(): array
    {
        return [
            'user' => [
                'id' => $this->userId,
            ],

            'staff' => [
                'id' => $this->staffId,
            ],

            'job_position' => [
                'id' => $this->jobPositionId,
            ],

            'team' => [
                'id' => $this->orgTeamId,
            ],

            'unit' => [
                'id' => $this->orgUnitId,
            ],

            'offering' => [
                'code' => $this->offeringCode,
            ],

            'request' => [
                'source' => $this->source,
            ],

            'metadata' => $this->metadata,
        ];
    }
}
