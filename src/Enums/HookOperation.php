<?php

declare(strict_types=1);

namespace Rimba\Work\Enums;

use Filament\Support\Contracts\HasLabel;

enum HookOperation: string implements HasLabel
{
    case Get = 'get';
    case Transform = 'transform';
    case Put = 'put';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Get => 'Get',
            self::Transform => 'Transform',
            self::Put => 'Put',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Get => 'Retrieve data from a secured REST Endpoint into FormData or Traveller.',
            self::Transform => 'Transform or derive values within FormData and Traveller.',
            self::Put => 'Send FormData or Traveller values to a secured REST Endpoint.',
        };
    }
}
