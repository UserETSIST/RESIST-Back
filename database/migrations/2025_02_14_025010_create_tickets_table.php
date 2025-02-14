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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();                               // Primary key
            $table->string('subject', 255);             // Ticket subject
            $table->text('description');                // Detailed description of the issue
            $table->enum('status', ['open', 'closed'])->default('open'); // Ticket status
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who created the ticket
            $table->timestamps();                       // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
