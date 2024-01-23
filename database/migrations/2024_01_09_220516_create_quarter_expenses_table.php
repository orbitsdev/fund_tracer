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
        Schema::create('quarter_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quarter_expense_budget_division_id')->nullable();
            $table->foreignId('fourth_layer_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarter_expenses');
    }
};
