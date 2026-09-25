<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

class WorkflowDefinition extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'slug';

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];

    public static function fromDefinition(array $definition): self
    {
        $model = new self;

        $model->setRawAttributes($definition, true);
        $model->exists = true;

        return $model;
    }

    public function save(array $options = []): bool
    {
        throw new LogicException(
            'Workflow definitions must be saved through WorkflowDefinitionRepository.'
        );
    }

    public function delete(): ?bool
    {
        throw new LogicException(
            'Workflow definitions must be deleted through WorkflowDefinitionRepository.'
        );
    }
}
