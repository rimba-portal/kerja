<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum WorkflowProviderType: string
{
    case Packstub = 'packstub';
    case Null = 'null';
}
