<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Rimba\Work\Support\HookContext;

final class HookPath
{
    public function read(
        string $path,
        HookContext $context,
        array $request = [],
        array $response = [],
    ): mixed {
        [$root, $nested] = $this->split($path);

        $source = match ($root) {
            'form' => $context->instance->form_data ?? [],
            'traveller' => $context->instance->traveller ?? [],
            'request' => $request,
            'response' => $response,
            'context' => $context->values(),
            'parameter' => $context->parameters,
            default => throw new InvalidArgumentException(
                "Unsupported Hook path root [{$root}]."
            ),
        };

        return $nested === ''
            ? $source
            : data_get($source, $nested);
    }

    public function writeInternal(
        string $path,
        mixed $value,
        HookContext $context,
    ): void {
        [$root, $nested] = $this->split($path);

        if (! in_array($root, ['form', 'traveller'], true)) {
            throw new InvalidArgumentException(
                "Hook cannot write internal value to root [{$root}]."
            );
        }

        $attribute = $root === 'form'
            ? 'form_data'
            : 'traveller';

        $data = $context->instance->{$attribute} ?? [];

        if ($nested === '') {
            if (! is_array($value)) {
                throw new InvalidArgumentException(
                    "Root [{$root}] must be assigned an array."
                );
            }

            $data = $value;
        } else {
            Arr::set($data, $nested, $value);
        }

        $context->instance->{$attribute} = $data;
    }

    public function writeRequest(
        array &$request,
        string $path,
        mixed $value,
    ): void {
        [$root, $nested] = $this->split($path);

        if ($root !== 'request') {
            throw new InvalidArgumentException(
                'Request mappings must target request.* paths.'
            );
        }

        if ($nested === '') {
            if (! is_array($value)) {
                throw new InvalidArgumentException(
                    'request root must be assigned an array.'
                );
            }

            $request = $value;

            return;
        }

        Arr::set($request, $nested, $value);
    }

    public function interpolate(
        string $value,
        HookContext $context,
    ): string {
        return (string) preg_replace_callback(
            '/\{((?:context|parameter)\.[A-Za-z0-9_.-]+)\}/',
            function (array $match) use ($context): string {
                $resolved = $this->read(
                    $match[1],
                    $context,
                );

                if (
                    $resolved === null
                    || is_array($resolved)
                    || is_object($resolved)
                ) {
                    throw new InvalidArgumentException(
                        "Unable to interpolate Hook path [{$match[1]}]."
                    );
                }

                return rawurlencode((string) $resolved);
            },
            $value,
        );
    }

    private function split(string $path): array
    {
        $parts = explode('.', $path, 2);

        return [
            $parts[0] ?? '',
            $parts[1] ?? '',
        ];
    }
}
