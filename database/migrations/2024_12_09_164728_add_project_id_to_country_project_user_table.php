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
        Schema::table('country_project_user', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable(true)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('country_project_user', function (Blueprint $table) {
            $table->dropColumn('project_id');
        });
    }
};
