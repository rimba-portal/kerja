<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Table(name: 'work_package_parties')]
#[Unguarded]
class PackageParty extends Model
{
    public function package(): BelongsTo
    {
        return $this->belongsTo(
            Package::class,
            'work_package_id',
        );
    }

    public function party(): MorphTo
    {
        return $this->morphTo();
    }
}
