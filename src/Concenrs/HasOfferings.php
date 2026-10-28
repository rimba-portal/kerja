<?php

declare(strict_types=1);

namespace Rimba\Work\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Rimba\Work\Models\Offering;

trait HasOfferings
{
    public function offerings(): HasMany
    {
        return $this->hasMany(
            Offering::class,
            'org_team_id',
            $this->getKeyName(),
        );
    }

    public function activeOfferings(): HasMany
    {
        return $this->offerings()
            ->active()
            ->ordered();
    }

    public function availableOfferings(): HasMany
    {
        return $this->offerings()
            ->available()
            ->ordered();
    }
}
