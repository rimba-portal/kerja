<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Carbon\CarbonImmutable;
use InvalidArgumentException;

final class ConverterRegistry
{
    public function convert(?string $converter, mixed $value, array $arguments = []): mixed
    {
        if (in_array($converter, [null, '', 'copy'], true)) {
            return $value;
        }

        return match ($converter) {
            'string' => $value === null ? null : (string) $value,
            'integer' => $value === null ? null : (int) $value,
            'decimal' => $value === null ? null : (float) $value,
            'boolean' => $this->toBoolean($value),
            'date' => $value === null ? null : CarbonImmutable::parse((string) $value)->toDateString(),
            'datetime' => $value === null ? null : CarbonImmutable::parse((string) $value)->toIso8601String(),
            'add' => $this->numeric($value) + $this->numeric($arguments['value'] ?? 0),
            'subtract' => $this->numeric($value) - $this->numeric($arguments['value'] ?? 0),
            default => throw new InvalidArgumentException("Converter [{$converter}] is not registered."),
        };
    }

    private function toBoolean(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ?? throw new InvalidArgumentException('Value cannot be converted to boolean.');
    }

    private function numeric(mixed $value): int|float
    {
        if (! is_numeric($value)) {
            throw new InvalidArgumentException('Converter requires a numeric value.');
        }

        return $value + 0;
    }
}
