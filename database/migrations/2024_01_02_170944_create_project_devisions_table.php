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
        Schema::create('project_devisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->nullable();
            $table->foreignId('project_quarter_id')->nullable();
            $table->foreignId('project_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_devisions');
    }
};
