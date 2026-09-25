<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;
enum ExecutionType: string
{
    case Human = 'human';
    case Rimba = 'rimba';
    case EventDriven = 'event_driven';

    public function createsInboxTask(): bool
    {
        return $this === self::Human;
    }

    public function executesImmediately(): bool
    {
        return $this === self::Rimba;
    }
}

