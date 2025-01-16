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
        Schema::table('time_sheet_projects', function (Blueprint $table) {
            $table->foreignId('country_project_id')->nullable(true)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_sheet_projects', function (Blueprint $table) {
            $table->dropColumn('country_project_id');
        });
    }
};
