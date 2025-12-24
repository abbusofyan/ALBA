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
		Schema::create('bin_type_waste', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bin_type_id');
            $table->unsignedBigInteger('waste_type_id');
            $table->timestamps();

            // Foreign key constraints (optional but recommended)
            $table->foreign('bin_type_id')->references('id')->on('bin_types')->onDelete('cascade');
            $table->foreign('waste_type_id')->references('id')->on('waste_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bin_type_waste');
    }
};
