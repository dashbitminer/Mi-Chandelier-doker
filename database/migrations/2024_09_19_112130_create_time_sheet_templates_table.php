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
        Schema::create('time_sheet_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(true)->constrained();
            $table->foreignId('country_id')->constrained();
            $table->integer('month');
            $table->integer('year');
            $table->enum('period', ['first_period', 'second_period', 'month'])->nullable(false)->default('month');
            $table->enum('status', ['unpublish', 'publish'])->nullable(false)->default('unpublish');
            $table->datetime('registered_at');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheet_templates');
    }
};
