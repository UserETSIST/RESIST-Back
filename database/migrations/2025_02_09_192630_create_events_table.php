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
            $table->id();
            $table->timestamps();
            $table->decimal('lat',14,12);
            $table->decimal('lon',15,12);
            $table->decimal('flightlevel',8,4);
            $table->dateTime('lastdetectiontimestamp');
            $table->boolean('jamming');
            $table->boolean('spoofing');
            $table->decimal('strength',5,4);
            $table->decimal('pfa',11,10);
            $table->string('datum',10);
            $table->string('sat_ua',20);
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
