<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'work_package_payloads')]
#[Unguarded]
class PackagePayload extends Model
{
    public function package(): BelongsTo
    {
        return $this->belongsTo(
            Package::class,
            'work_package_id',
        );
    }
}
