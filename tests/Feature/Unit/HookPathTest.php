<?php

declare(strict_types=1);

namespace Rimba\Work\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Rimba\Work\Models\FlowInstance;
use Rimba\Work\Services\HookPath;
use Rimba\Work\Support\HookContext;

final class HookPathTest extends TestCase
{
    public function test_it_interpolates_context_and_parameter_paths(): void
    {
        $flowInstance = new FlowInstance([
            'form_data' => [],
            'traveller' => [],
        ]);

        $flowInstance->id = 25;
        $flowInstance->uuid = 'flow-uuid';

        $hookContext = new HookContext(
            instance: $flowInstance,
            parameters: [
                'document_id' => 99,
            ],
        );

        $path = app(HookPath::class)->interpolate(
            '/flows/{context.workflow_instance.id}/documents/{parameter.document_id}',
            $hookContext,
        );

        $this->assertSame(
            '/flows/25/documents/99',
            $path,
        );
    }
}
