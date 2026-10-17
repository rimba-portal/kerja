<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Illuminate\Database\Eloquent\Model;
use Rimba\Work\Enums\HookPoint;
use Rimba\Work\Support\HookContext;

final class HookBindingRunner
{
    public function __construct(private HookRunner $hookRunner) {}

    public function runFor(Model $hookable, HookPoint $point, HookContext $context): void
    {
        if (! method_exists($hookable, 'hookBindings')) {
            return;
        }

        $bindings = $hookable->hookBindings()
            ->with('hook')
            ->where('hook_point', $point->value)
            ->where('is_enabled', true)
            ->orderBy('sequence')
            ->orderBy('id')
            ->get();

        foreach ($bindings as $binding) {
            $this->hookRunner->run($binding, $context);
        }
    }
}
