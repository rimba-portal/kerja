<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use RuntimeException;

class HandlerRegistry
{
    public function resolve(string $alias): object
    {
        $handler = config("sipoc.handlers.{$alias}", $alias);

        if (! is_string($handler) || ! class_exists($handler)) {
            throw new RuntimeException(
                "SIPOC handler [{$alias}] is not registered."
            );
        }

        return app($handler);
    }
}
