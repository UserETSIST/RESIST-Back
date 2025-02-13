<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        

        Schema::create('events', function (Blueprint $table) {
            $table->id();                                // Clave primaria (bigint auto-increment)
            $table->decimal('latitude', 14, 12);           // Latitud con 8 decimales de precisión
            $table->decimal('longitude', 15, 12);          // Longitud con 8 decimales de precisión
            $table->decimal('flightlevel', 8, 4);         // Nivel de vuelo con 4 decimales de precisión
            $table->dateTime('last_detection');           // Fecha y hora de la última detección
            $table->boolean('jamming')->default(false);   // Evento de jamming (valor por defecto: false)
            $table->boolean('spoofing')->default(false);  // Evento de spoofing (valor por defecto: false)
            $table->decimal('strength', 5, 4);            // Fuerza del ataque con precisión de 4 decimales
            $table->decimal('pfa', 11, 10);                 // Probabilidad de falsa alarma entre 0 y 1
            $table->string('datum', 10)->nullable();      // Sistema de referencia geodésico (ejemplo: WGS84)
            $table->string('sat_ua');  // Número de satélites (reemplazado `string` por `smallInteger`)
            $table->timestamps();                         // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
