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
        Schema::create('country_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('project_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->boolean('require_time_sheet')->nullable(false)->default(false);
            $table->integer('saturday_hours')->default(0);
            $table->integer('sunday_hours')->default(0);
            $table->timestamps();
            $table->unique(['country_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_projects');
    }
};
