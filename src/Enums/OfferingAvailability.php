<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum OfferingAvailability: string
{
    case Available = 'available';
    case Internal = 'internal';
    case Suspended = 'suspended';
    case Retired = 'retired';
}
