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
        Schema::create('user_course_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_topic_id')->constrained();
            $table->foreignId('user_course_id')->constrained();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->enum('evaluation_status', ['pending', 'failed', 'approved'])->default('pending');
            $table->string('note')->default('0')->nullable(false);
            $table->boolean('require_evaluation')->nullable(false)->default(false);
            $table->date('start_date');
            $table->date('evaluation_date')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_course_topics');
    }
};
