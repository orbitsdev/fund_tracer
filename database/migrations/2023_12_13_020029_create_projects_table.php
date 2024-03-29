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
            $table->string('title')->nullable();
            $table->string('project_type')->nullable();
            $table->string('implementing_agency')->nullable();
            $table->string('monitoring_agency')->nullable();
            $table->string('project_leader')->nullable();
            $table->decimal('allocated_fund', 20, 2)->nullable();
            $table->decimal('total_usage', 20, 2)->nullable()->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
