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
        Schema::create('time_sheet_project_weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_sheet_id')->constrained();
            $table->foreignId('time_sheet_project_id')->constrained();
            $table->foreignId('time_sheet_week_id')->constrained();
            $table->text('comment')->nullable();
            $table->foreignId('project_id')->constrained();
            $table->integer('week_of_year')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheet_project_weeks');
    }
};
