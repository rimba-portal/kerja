<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'work_endpoints')]
#[Unguarded]
class Endpoint extends Model
{
    protected function casts(): array
    {
        return [
            'timeout_seconds' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
