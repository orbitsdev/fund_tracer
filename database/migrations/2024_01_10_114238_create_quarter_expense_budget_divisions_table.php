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
        Schema::create('quarter_expense_budget_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_quarter_id')->nullable();
            $table->foreignId('project_devision_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarter_expense_budget_divisions');
    }
};
