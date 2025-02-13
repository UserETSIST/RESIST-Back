<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller;

class EventController extends Controller
{
    /**
     * Obtener eventos filtrados por rango de fecha y condiciones opcionales.
     */
    public function getFilteredEvents(Request $request)
    {
        try {
            // Validar el request
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'jamming' => 'nullable|boolean',
                'spoofing' => 'nullable|boolean',
            ]);

            // Obtener eventos utilizando el modelo `Event`
            $events = Event::getEventsByConditions(
                $validated['start_date'],
                $validated['end_date'],
                $request->input('jamming'),
                $request->input('spoofing')
            );

            // Respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => 'Events retrieved successfully',
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            // Manejar errores de validación
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Exception $e) {
            // Manejar errores generales
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving events',
                'error' => $e->getMessage(),  // Opcional: Ocultar en producción
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener eventos recientes en los últimos N días con condiciones opcionales.
     */
    public function getRecentEvents(Request $request)
    {
        try {
            // Validar el request
            $validated = $request->validate([
                'days' => 'required|numeric',
                'jamming' => 'nullable|boolean',
                'spoofing' => 'nullable|boolean',
            ]);

            // Obtener eventos recientes utilizando el modelo `Event`
            $events = Event::getRecentEvents(
                $validated['days'],
                $request->input('jamming'),
                $request->input('spoofing')
            );

            // Respuesta exitosa
            return response()->json([
                'success' => true,
                'data' => $events,
                'message' => "Events from the last {$validated['days']} days retrieved successfully",            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            // Manejar errores de validación
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Exception $e) {
            // Manejar errores generales
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving events',
                'error' => $e->getMessage(),  // Opcional: Ocultar en producción
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
