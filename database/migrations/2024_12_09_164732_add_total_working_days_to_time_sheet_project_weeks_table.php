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
        Schema::table('time_sheet_project_weeks', function (Blueprint $table) {
            $table->integer('total_working_days')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_sheet_project_weeks', function (Blueprint $table) {
            $table->dropColumn('total_working_days');
        });
    }
};
