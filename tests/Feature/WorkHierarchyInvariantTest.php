<?php

declare(strict_types=1);

namespace Rimba\Work\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rimba\Work\Models\Flow;
use Rimba\Work\Services\FlowDefinitionValidator;
use Rimba\Work\Tests\TestCase;

final class WorkHierarchyInvariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_active_workflow_is_valid(): void
    {
        $flowDefinitionValidator = app(
            FlowDefinitionValidator::class
        );

        Flow::query()
            ->where('status', 'active')
            ->each(
                fn (Flow $workflow) => $flowDefinitionValidator->validate($workflow)
            );

        $this->assertTrue(true);
    }
}
