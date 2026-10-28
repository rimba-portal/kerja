<?php

declare(strict_types=1);

namespace Rimba\Work\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rimba\Work\Models\Lifecycle;
use Rimba\Work\Models\LifecyclePhase;

trait HasLifecycle
{
    public function lifecycle(): BelongsTo
    {
        return $this->belongsTo(
            Lifecycle::class,
            'lifecycle_code',
            'code',
        );
    }

    public function lifecyclePhase(): BelongsTo
    {
        return $this->belongsTo(
            LifecyclePhase::class,
            'lifecycle_phase_code',
            'code',
        );
    }

    public function lifecyclePhaseQuery()
    {
        return LifecyclePhase::query()
            ->where(
                'lifecycle_code',
                $this->lifecycle_code,
            )
            ->where(
                'code',
                $this->lifecycle_phase_code,
            );
    }

    public function transitionLifecycleTo(
        LifecyclePhase|string $phase,
    ): static {
        $phaseCode = $phase instanceof LifecyclePhase
            ? $phase->code
            : $phase;

        $lifecyclePhase = LifecyclePhase::query()
            ->where(
                'lifecycle_code',
                $this->lifecycle_code,
            )
            ->where(
                'code',
                $phaseCode,
            )
            ->firstOrFail();

        $this->forceFill([
            'lifecycle_phase_code' => $lifecyclePhase->code,
        ])->save();

        return $this->refresh();
    }
}
