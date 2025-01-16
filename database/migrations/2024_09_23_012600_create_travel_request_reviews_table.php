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
        Schema::create('travel_request_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_request_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->text('comment')->nullable();
            $table->enum('status', ['approved', 'denied', 'rejected'])->nullable();
            $table->enum('queue', ['pending', 'completed'])->default('completed');
            $table->datetime('registered_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_request_reviews');
    }
};
