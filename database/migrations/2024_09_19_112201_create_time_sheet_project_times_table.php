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
        Schema::create('time_sheet_project_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_sheet_id')->constrained();
            $table->foreignId('time_sheet_project_id')->constrained();
            $table->date('date')->nullable(false);
            $table->integer('hours')->default(0);
            $table->unsignedBigInteger('absence_type_id')->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('project_id')->constrained();
            $table->integer('original_hours')->nullable();
            $table->boolean('customized')->nullable(false)->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheet_project_times');
    }
};
