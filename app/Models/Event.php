<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    protected $table = 'events';
    public $timestamps = false;  // Deshabilitar timestamps

    protected $fillable = [
        'latitude', 'longitude', 'flightlevel', 'last_detection',
        'jamming', 'spoofing', 'pfa', 'datum', 'sat_ua'
    ];

    /**
     * Obtener eventos filtrados por rango de fechas y condiciones opcionales.
     */
    public static function getEventsByConditions($startDate, $endDate, $jammer = null, $spoofer = null)
    {
        $query = DB::table('events')
            ->whereBetween('last_detection', [$startDate, $endDate]);

        if ($jammer && !$spoofer) {
            $query->where('jamming', 1);
        } elseif (!$jammer && $spoofer) {
            $query->where('spoofing', 1);
        }

        $events = $query->orderBy('last_detection', 'desc')->get();

        return self::formatEvents($events);
    }

    /**
     * Obtener eventos recientes en los últimos N días, filtrados opcionalmente por jamming o spoofing.
     */
    public static function getRecentEvents($days, $jammer = null, $spoofer = null)
    {
        $query = DB::table('events')
            ->where('last_detection', '>=', Carbon::now()->subDays($days))
            ->orderBy('last_detection', 'desc');

        if ($jammer && !$spoofer) {
            $query->where('jamming', 1);
        } elseif (!$jammer && $spoofer) {
            $query->where('spoofing', 1);
        }

        $events = $query->get();

        return self::formatEvents($events);
    }

    /**
     * Formatear eventos: convertir latitud y longitud a hexadecimal y eliminar campos no deseados.
     */
    private static function formatEvents($events)
    {
        $scale = 1e5;

        return $events->map(function ($event) use ($scale) {
            // Convert latitude to hexadecimal
            $latValue = abs($event->latitude);
            $latScaled = round($latValue * $scale);
            $latHex = strtoupper(dechex($latScaled));
            $event->lat_hex = ($event->latitude < 0 ? '-' : '') . '0x' . $latHex;

            // Convert longitude to hexadecimal
            $lonValue = abs($event->longitude);
            $lonScaled = round($lonValue * $scale);
            $lonHex = strtoupper(dechex($lonScaled));
            $event->lon_hex = ($event->longitude < 0 ? '-' : '') . '0x' . $lonHex;

            // Remove unwanted fields
            unset($event->id, $event->sat_ua, $event->latitude, $event->longitude, $event->pfa, $event->datum);

            return $event;
        });
    }
}
