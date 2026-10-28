<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum SubjectCreationMode: string
{
    case Create = 'create';
    case Select = 'select';
    case Update = 'update';
    case None = 'none';
}
