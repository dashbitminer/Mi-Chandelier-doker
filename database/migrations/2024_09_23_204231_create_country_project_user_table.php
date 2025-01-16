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
        Schema::create('country_project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_project_id')->constrained()->nullOnDelete()->references('id');
            $table->foreignId('user_id')->constrained()->nullOnDelete();
            $table->boolean('is_leader')->default(false);
            $table->timestamps();
            $table->unique(['country_project_id', 'user_id']);
            $table->decimal('salary_distribution', 8, 2)->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_project_user');
    }
};
