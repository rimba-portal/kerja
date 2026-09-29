<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum DefinitionStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
