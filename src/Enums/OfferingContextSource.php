<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum OfferingContextSource: string
{
    case Filament = 'filament';
    case Api = 'api';
    case Web = 'web';
    case System = 'system';
}
