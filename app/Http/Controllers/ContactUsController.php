<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class ContactUsController extends Controller
{
    /**
     * Almacenar un nuevo mensaje de contacto.
     */
    public function store(Request $request)
    {
        try {
            // Validar la solicitud
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'message' => 'required|string',
            ]);

            // Llamar a la función del modelo para crear el mensaje
            ContactUs::createMessage($validated);

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully',
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending your message',
                'error' => $e->getMessage(),  // Opcional: Ocultar en producción
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Listar todos los mensajes de contacto.
     */
    public function index()
    {
        try {
            // Llamar a la función del modelo para obtener los mensajes
            $messages = ContactUs::getAllMessages();

            return response()->json([
                'success' => true,
                'data' => $messages,
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving messages',
                'error' => $e->getMessage(),  // Opcional: Ocultar en producción
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
