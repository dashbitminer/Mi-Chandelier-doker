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
        Schema::create('time_sheet_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_sheet_id')->constrained();
            $table->foreignId('project_id')->constrained();
            $table->decimal('percentage', 8, 2)->nullable(false)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheet_projects');
    }
};
