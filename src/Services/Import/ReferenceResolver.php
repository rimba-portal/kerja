<?php

declare(strict_types=1);

namespace Rimba\Work\Services\Import;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Rimba\Work\Exceptions\CatalogReferenceNotFound;
use RuntimeException;

final class ReferenceResolver
{
    public function id(
        string $table,
        string $code,
        string $type,
    ): int {
        $id = DB::table($table)
            ->where('code', $code)
            ->value('id');

        if ($id === null) {
            throw CatalogReferenceNotFound::for($type, $code);
        }

        return (int) $id;
    }

    public function externalId(
        string $kind,
        string $code,
    ): int {
        $model = config("bites.kerja.models.{$kind}");

        if (! is_string($model) || ! class_exists($model)) {
            throw new RuntimeException(
                "Kerja external model [{$kind}] is not configured."
            );
        }

        /** @var class-string<Model> $model */
        return $this->modelId(
            $model,
            $code,
            str_replace('_', ' ', $kind),
        );
    }

    /**
     * @param  class-string<Model>  $model
     */
    public function modelId(
        string $model,
        string $code,
        string $type,
    ): int {
        $id = $model::query()
            ->where('code', $code)
            ->value('id');

        if ($id === null) {
            throw CatalogReferenceNotFound::for($type, $code);
        }

        return (int) $id;
    }

    public function flowId(string $code): int
    {
        return $this->id(
            'work_flows',
            $code,
            'flow',
        );
    }

    public function stepId(
        int $flowId,
        string $code,
    ): int {
        $id = DB::table('work_flow_steps')
            ->where('workflow_id', $flowId)
            ->where('code', $code)
            ->value('id');

        if ($id === null) {
            throw CatalogReferenceNotFound::for(
                'flow step',
                $code,
            );
        }

        return (int) $id;
    }

    public function actionId(
        string $flowCode,
        string $stepCode,
        string $actionType,
    ): int {
        $flowId = $this->flowId($flowCode);
        $stepId = $this->stepId($flowId, $stepCode);

        $id = DB::table('work_flow_actions')
            ->where('workflow_step_id', $stepId)
            ->where('action_type', $actionType)
            ->value('id');

        if ($id === null) {
            throw CatalogReferenceNotFound::for(
                'flow action',
                "{$flowCode}.{$stepCode}.{$actionType}",
            );
        }

        return (int) $id;
    }
}
