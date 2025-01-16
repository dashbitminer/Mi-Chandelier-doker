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
        Schema::create('time_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->enum('status', ['incomplete', 'completed', 'pending', 'approved', 'rejected'])->default('incomplete');
            $table->datetime('registered_at');
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('user_acceptance')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('period', ['first_period', 'second_period', 'month'])->nullable(false)->default('month');
            $table->foreignId('time_sheet_template_id')->nullable(true)->constrained();
            $table->foreignId('reviewer_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheets');
    }
};
