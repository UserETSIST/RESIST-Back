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
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();                                // Clave primaria (bigint auto-increment)
            $table->string('name', 255);                 // Nombre del remitente (máximo 255 caracteres)
            $table->string('email', 255);                // Email del remitente (máximo 255 caracteres)
            $table->string('phone', 20)->nullable();     // Teléfono opcional (máximo 20 caracteres)
            $table->text('message');                     // Mensaje de contacto
            $table->dateTime('created_at'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_us');
    }
};
