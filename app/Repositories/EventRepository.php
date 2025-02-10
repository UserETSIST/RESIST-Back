<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EventRepository
{
    public function getAll()
    {
        return DB::table('events')->get();
    }

    /**
     * Get events based on jammer, spoofer, and date range conditions.
     *
     * @param string $startDate
     * @param string $endDate
     * @param bool|null $jammer
     * @param bool|null $spoofer
     * @return \Illuminate\Support\Collection
     */
    public function getEventsByConditions($startDate, $endDate, $jammer = null, $spoofer = null)
    {
        $query = DB::table('events')
            ->whereBetween('lastdetectiontimestamp', [$startDate, $endDate]);

        // Add conditions based on the user selection
        if ($jammer) {
            $query->where('jamming', 1);
        }

        if ($spoofer) {
            $query->where('spoofing', 1);
        }

        return $query->orderBy('lastdetectiontimestamp', 'desc')->get();
    }

    public function getSevenPastDaysEvents($jamming = null, $spoofing = null)
    {
        $query = DB::table('events')
            ->where('lastdetectiontimestamp', '>=', Carbon::now()->subDays(7))  // Events from the last 7 days
            ->orderBy('lastdetectiontimestamp', 'desc');

        // Add jamming filter if provided
        if (!is_null($jamming)) {
            $query->where('jamming', $jamming ? 1 : 0);
        }

        // Add spoofing filter if provided
        if (!is_null($spoofing)) {
            $query->where('spoofing', $spoofing ? 1 : 0);
        }

        return $query->get();
    }


}
