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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('category');
            // $table->string('frequency');
            // $table->text('notes');
            // $table->text('financial_statements')->nullable();
            // $table->text('documentation')->nullable();
            // $table->string('approval_status')->default('pending');
            $table->text('financial_statements')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
