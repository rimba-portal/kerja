<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum FormProviderType: string
{
    case Packstub = 'packstub';
    case Local = 'local';
}
