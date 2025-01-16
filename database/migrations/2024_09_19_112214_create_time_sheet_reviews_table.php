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
        Schema::create('time_sheet_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_sheet_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->text('comment')->nullable();
            $table->enum('status', ['approved', 'rejected'])->nullable();
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
        Schema::dropIfExists('time_sheet_reviews');
    }
};
