<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


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

        if ($jammer) {
            $query->where('jamming', 1);
        }

        if ($spoofer) {
            $query->where('spoofing', 1);
        }

        $events = $query->orderBy('lastdetectiontimestamp', 'desc')->get();

        $modifiedEvents = $events->map(function ($event) {
            $scale = 1e5;  // Scale factor for 5-decimal precision

            // Convert latitude to hexadecimal
            $latValue = abs($event->lat);
            $latScaled = round($latValue * $scale);
            $latHex = strtoupper(dechex($latScaled));
            $event->lat_hex = ($event->lat < 0 ? '-' : '') . '0x' . $latHex;

            // Convert longitude to hexadecimal
            $lonValue = abs($event->lon);
            $lonScaled = round($lonValue * $scale);
            $lonHex = strtoupper(dechex($lonScaled));
            $event->lon_hex = ($event->lon < 0 ? '-' : '') . '0x' . $lonHex;

            // Remove unwanted fields
            unset($event->id, $event->sat_ua, $event->lat, $event->lon, $event->pfa, $event->datum);

            return $event;
        });

        return $modifiedEvents;
    }


    public function getRecentEvents($days, $jammer = null, $spoofer = null)
    {
        $query = DB::table('events')
            ->where('lastdetectiontimestamp', '>=', Carbon::now()->subDays($days))
            ->orderBy('lastdetectiontimestamp', 'desc');

        // Apply conditions based on user selection
        if ($jammer && !$spoofer) {
            // Jamming only
            $query->where('jamming', 1);
        } elseif (!$jammer && $spoofer) {
            // Spoofing only
            $query->where('spoofing', 1);
        }
        // If both are selected or both are null, no additional filters are applied for jamming/spoofing

        $events = $query->get();

        // Convert latitude and longitude to hexadecimal and remove unwanted fields
        $modifiedEvents = $events->map(function ($event) {
            $scale = 1e5;

            // Convert latitude to hexadecimal
            $latValue = abs($event->lat);
            $latScaled = round($latValue * $scale);
            $latHex = strtoupper(dechex($latScaled));
            $event->lat_hex = ($event->lat < 0 ? '-' : '') . '0x' . $latHex;

            // Convert longitude to hexadecimal
            $lonValue = abs($event->lon);
            $lonScaled = round($lonValue * $scale);
            $lonHex = strtoupper(dechex($lonScaled));
            $event->lon_hex = ($event->lon < 0 ? '-' : '') . '0x' . $lonHex;

            // Remove unwanted fields
            unset($event->id, $event->sat_ua, $event->lat, $event->lon, $event->pfa, $event->datum);

            return $event;
        });

        return $modifiedEvents;
    }








}
