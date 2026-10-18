<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'work_business_objects')]
#[Unguarded]
class BusinessObject extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function lifecycles(): HasMany
    {
        return $this->hasMany(Lifecycle::class);
    }

    public function flows(): HasMany
    {
        return $this->hasMany(Flow::class, 'business_object_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function resolveModelClass(): ?string
    {
        return $this->model_class && class_exists($this->model_class) ? $this->model_class : null;
    }
}
