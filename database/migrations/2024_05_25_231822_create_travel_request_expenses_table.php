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
        Schema::create('travel_request_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('comment')->nullable();
            $table->foreignId('travel_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_kind_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2)->nullable(false)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_request_expenses');
    }
};
