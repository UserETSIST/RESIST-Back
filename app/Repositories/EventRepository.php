<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EventRepository
{
    public function getAllEventsPaginated($perPage = 10)
    {
        return DB::table('events')
            ->orderBy('lastdetectiontimestamp', 'desc')  // Sort by most recent events
            ->paginate($perPage);
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

    public function getRecentEvents($days,$jammer = null, $spoofer = null)
    {
        $query = DB::table('events')
            ->where('lastdetectiontimestamp', '>=', Carbon::now()->subDays($days))  // Events from the last days
            ->orderBy('lastdetectiontimestamp', 'desc');

        // Add jamming filter if provided
        if ($jammer) {
            $query->where('jamming', 1);
        }

        // Add spoofing filter if provided
        if ($spoofer) {
            $query->where('spoofing', 1);
        }

        return $query->get();
    }


}
