<?php

declare(strict_types=1);

namespace Rimba\Work\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'work_packages')]
#[Unguarded]
class Package extends Model
{
    public function activityType(): BelongsTo
    {
        return $this->belongsTo(
            ActivityType::class,
            'activity_type_id',
        );
    }

    public function businessObject(): BelongsTo
    {
        return $this->belongsTo(
            BusinessObject::class,
            'business_object_id',
        );
    }

    public function orgTeam(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.kerja.models.org_team'),
            'org_team_id',
        );
    }

    public function actorJobRole(): BelongsTo
    {
        return $this->belongsTo(
            config('bites.kerja.models.job_role'),
            'actor_job_role_id',
        );
    }

    public function parties(): HasMany
    {
        return $this->hasMany(
            PackageParty::class,
            'work_package_id',
        );
    }

    public function suppliers(): HasMany
    {
        return $this->parties()
            ->where('side', 'supplier');
    }

    public function customers(): HasMany
    {
        return $this->parties()
            ->where('side', 'customer');
    }

    public function payloads(): HasMany
    {
        return $this->hasMany(
            PackagePayload::class,
            'work_package_id',
        );
    }

    public function inputs(): HasMany
    {
        return $this->payloads()
            ->where('direction', 'input');
    }

    public function outputs(): HasMany
    {
        return $this->payloads()
            ->where('direction', 'output');
    }
}
