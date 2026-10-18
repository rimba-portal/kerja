<?php

declare(strict_types=1);

namespace Rimba\Work\Tests\Feature;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rimba\Work\Models\FlowInitiator;
use Rimba\Work\Models\FlowInstance;
use Rimba\Work\Models\FlowOwner;
use Rimba\Work\Models\FlowStep;
use Rimba\Work\Models\PackageParty;
use Rimba\Work\Models\PackagePayload;
use Rimba\Work\Models\Task;

final class ModelRelationshipTest extends TestCase
{
    #[DataProvider('belongsToRelationships')]
    public function test_relationship_uses_expected_foreign_key(
        string $model,
        string $relationship,
        string $foreignKey,
    ): void {
        $relation = (new $model)
            ->{$relationship}();

        $this->assertInstanceOf(
            BelongsTo::class,
            $relation,
        );

        $this->assertSame(
            $foreignKey,
            $relation->getForeignKeyName(),
        );
    }

    public static function belongsToRelationships(): array
    {
        return [
            [
                Task::class,
                'flowInstance',
                'workflow_instance_id',
            ],
            [
                Task::class,
                'flowStep',
                'workflow_step_id',
            ],
            [
                Task::class,
                'package',
                'work_package_id',
            ],
            [
                FlowStep::class,
                'flow',
                'workflow_id',
            ],
            [
                FlowStep::class,
                'package',
                'work_package_id',
            ],
            [
                FlowInstance::class,
                'flow',
                'workflow_id',
            ],
            [
                FlowInstance::class,
                'currentFlowStep',
                'current_workflow_step_id',
            ],
            [
                FlowOwner::class,
                'flow',
                'workflow_id',
            ],
            [
                FlowInitiator::class,
                'flow',
                'workflow_id',
            ],
            [
                PackageParty::class,
                'package',
                'work_package_id',
            ],
            [
                PackagePayload::class,
                'package',
                'work_package_id',
            ],
        ];
    }
}
