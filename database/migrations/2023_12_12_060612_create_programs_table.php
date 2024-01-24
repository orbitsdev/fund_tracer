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
        Schema::create('programs', function (Blueprint $table) {


            $table->id();

            // $table->string('status')->nullable()->default('pending');
            $table->string('title')->nullable();
            $table->string('program_leader')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('total_budget', 20, 2)->nullable()->default(0);
            $table->decimal('total_usage', 20, 2)->nullable()->default(0);
            $table->string('implementing_agency')->nullable();
            $table->string('monitoring_agency')->nullable();
            $table->string('status')->nullable()->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
