<?php

namespace App\Traits;

use App\Models\Charge;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

trait HasChargeLogic
{
    /**
     * Get the next charge number for a specific user and period.
     */
    protected function nextChargeNumberForUser(?int $userId, ?string $period = null): string
    {
        if (! $userId) {
            return '1';
        }

        $period = $period ?? $this->getChargePeriod();
        $query = Charge::where('user_id', $userId);

        if ($period) {
            $query->where('charge_period', $period);
        } else {
            $query->whereNull('charge_period');
        }

        $maxValue = $query->max(DB::raw('CAST(n_charge as UNSIGNED)'));
        $nextValue = ((int) $maxValue) + 1;

        return (string) $nextValue;
    }

    /**
     * Get the current charge period from settings.
     */
    protected function getChargePeriod(): ?string
    {
        $period = Setting::getValue('charge_period', '');

        return $period !== '' ? $period : null;
    }
}
