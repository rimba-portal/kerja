<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'work_package_payloads')]
class WorkPackagePayload extends Model
{
    protected $guarded = [];

    public function workPackage(): BelongsTo
    {
        return $this->belongsTo(WorkPackage::class);
    }
}
