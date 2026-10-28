<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum LifecycleCategory: string
{
    case Governance = 'governance';
    case People = 'people';
    case Knowledge = 'knowledge';
    case Customer = 'customer';
    case Product = 'product';
    case Supply = 'supply';
    case Operations = 'operations';
    case Asset = 'asset';
    case Quality = 'quality';
    case Safety = 'safety';
    case Security = 'security';
    case Finance = 'finance';
    case Technology = 'technology';
    case Improvement = 'improvement';
}
