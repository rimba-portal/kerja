<?php

declare(strict_types=1);

namespace Rimba\Work\Tests\Feature;

use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rimba\Work\Models\LifecyclePhase;
use Rimba\Work\Models\Package;

final class HasManyRelationshipTest extends TestCase
{
    #[DataProvider('relationships')]
    public function test_relationship_uses_expected_foreign_key(
        string $model,
        string $relationship,
        string $foreignKey,
    ): void {
        $relation = (new $model)
            ->{$relationship}();

        $this->assertInstanceOf(
            HasMany::class,
            $relation,
        );

        $this->assertSame(
            $foreignKey,
            $relation->getForeignKeyName(),
        );
    }

    public static function relationships(): array
    {
        return [
            [
                LifecyclePhase::class,
                'flows',
                'lifecycle_phase_id',
            ],
            [
                Package::class,
                'parties',
                'work_package_id',
            ],
            [
                Package::class,
                'payloads',
                'work_package_id',
            ],
        ];
    }
}
