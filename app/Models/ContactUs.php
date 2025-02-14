<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;

class ContactUs extends Model
{
    use HasFactory;

    public $timestamps = false;  // Deshabilitar timestamps

    protected $table = 'contact_us';

    protected $fillable = [
        'name', 'email', 'phone', 'message', 'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Crear un nuevo mensaje de contacto.
     */
    public static function createMessage(array $data)
    {
        try {
            return self::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'message' => $data['message'],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            throw new Exception("Failed to create message: " . $e->getMessage());
        }
    }

    /**
     * Obtener todos los mensajes de contacto.
     */
    public static function getAllMessages()
    {
        try {
            return self::orderBy('created_at', 'desc')->get();
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve messages: " . $e->getMessage());
        }
    }
}

