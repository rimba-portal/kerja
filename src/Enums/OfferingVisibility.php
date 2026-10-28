<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum OfferingVisibility: string
{
    case Private = 'private';
    case Team = 'team';
    case Organization = 'organization';
    case Public = 'public';
}
