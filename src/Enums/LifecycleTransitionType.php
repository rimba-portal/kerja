<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum LifecycleTransitionType: string
{
    case Stay = 'stay';
    case Advance = 'advance';
    case Transition = 'transition';
}
