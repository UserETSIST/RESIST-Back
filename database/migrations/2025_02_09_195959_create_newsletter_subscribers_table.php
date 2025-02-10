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
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();  // Unique to prevent duplicate subscriptions
            $table->string('subscription_status')->default('subscribed'); // 'subscribed', 'unsubscribed', 'pending'
            $table->timestamp('subscribed_at')->nullable(); // When the user subscribed
            $table->timestamp('unsubscribed_at')->nullable(); // When the user unsubscribed (if applicable)
            $table->timestamps(); // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
