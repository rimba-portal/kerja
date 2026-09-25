<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

enum ActivityType: string
{
    case Create = 'create';
    case Capture = 'capture';
    case Verify = 'verify';
    case Analyze = 'analyze';
    case Decide = 'decide';
    case Authorize = 'authorize';
    case Transform = 'transform';
    case Execute = 'execute';
    case Record = 'record';
    case Communicate = 'communicate';

    public function label(): string
    {
        return str($this->value)->headline()->toString();
    }
}
