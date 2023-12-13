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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable();
            $table->foreignId('program_id')->nullable();
            $table->string('title');
            $table->decimal('allocated_fund', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->nullable()->default('Not Active');
            // $table->decimal('expenses', 10, 2);
            // $table->string('status');
            // $table->foreignId('user_id')->constrained();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
