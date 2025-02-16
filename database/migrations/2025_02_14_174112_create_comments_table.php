<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();                           // Primary key
            $table->text('comment');                // Comment text
            $table->foreignId('ticket_id')          // Foreign key to the tickets table
                  ->constrained('tickets')
                  ->onDelete('cascade');
            $table->foreignId('user_id')            // Foreign key to the users table
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();                   // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
