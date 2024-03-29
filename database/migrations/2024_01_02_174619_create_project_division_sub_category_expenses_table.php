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
        Schema::create('project_division_sub_category_expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_division_category_id')->nullable();
            $table->foreignId('project_division_sub_category_id')->nullable();
            // $table->foreignId('project_quarter_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('parent_title')->nullable();
            $table->string('title')->nullable();
            // $table->text('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_division_sub_category_expenses');
    }
};
