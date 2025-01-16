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
        Schema::create('user_course_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_evaluation_question_option_id')->constrained();
            $table->foreignId('course_evaluation_question_id')->constrained();
            $table->foreignId('user_course_topic_id')->constrained();
            $table->boolean('is_correct')->nullable(false)->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_course_evaluations');
    }
};
