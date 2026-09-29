<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum CompletionEffect: string
{
    case Continue = 'continue';
    case Complete = 'complete';
    case Cancel = 'cancel';
}
