<?php

namespace App\Observers;

use App\Models\SelenggaraKenderaan;
use Carbon\Carbon;

class SelenggaraKenderaanObserver
{
    /**
     * Handle the SelenggaraKenderaan "creating" event.
     * Auto-set status based on dates when creating.
     */
    public function creating(SelenggaraKenderaan $selenggara): void
    {
        $this->updateStatusBasedOnDates($selenggara);
    }

    /**
     * Handle the SelenggaraKenderaan "updating" event.
     * Auto-update status based on dates when updating.
     */
    public function updating(SelenggaraKenderaan $selenggara): void
    {
        $this->updateStatusBasedOnDates($selenggara);
    }

    /**
     * Update status based on tarikh_mula and tarikh_selesai.
     */
    private function updateStatusBasedOnDates(SelenggaraKenderaan $selenggara): void
    {
        // Get dates
        $tarikh_mula = Carbon::parse($selenggara->tarikh_mula);
        $tarikh_selesai = Carbon::parse($selenggara->tarikh_selesai);
        $today = Carbon::today();

        // Auto-determine status based on dates
        if ($today->lt($tarikh_mula)) {
            // Today is before start date → Dijadualkan
            $selenggara->status = 'dijadualkan';
        } elseif ($today->between($tarikh_mula, $tarikh_selesai)) {
            // Today is between start and end date → Dalam Proses
            $selenggara->status = 'dalam_proses';
        } else {
            // Today is after end date → Selesai
            $selenggara->status = 'selesai';
        }
    }
}
