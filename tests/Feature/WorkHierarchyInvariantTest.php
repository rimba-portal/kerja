<?php

declare(strict_types=1);

namespace Rimba\Work\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rimba\Work\Models\LifecyclePhase;
use Rimba\Work\Models\WorkFlow;
use Rimba\Work\Tests\TestCase;

final class WorkHierarchyInvariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_phase_has_a_workflow(): void
    {
        $this->assertFalse(LifecyclePhase::query()->doesntHave('workflows')->exists());
    }

    public function test_every_workflow_has_owner_initiator_and_start_step(): void
    {
        WorkFlow::query()->each(function (WorkFlow $w): void {
            $this->assertTrue($w->owners()->exists(), $w->code.' has no owner.');
            $this->assertTrue($w->initiators()->exists(), $w->code.' has no initiator.');
            $this->assertNotNull($w->startStep(), $w->code.' has no start step.');
        });
    }
}
