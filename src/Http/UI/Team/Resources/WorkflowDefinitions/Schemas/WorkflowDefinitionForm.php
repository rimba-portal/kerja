<?php

declare(strict_types=1);

namespace Rimba\Work\Http\UI\Team\Resources\WorkflowDefinitions\Schemas;

use Filament\Schemas\Schema;

class WorkflowDefinitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }
}
