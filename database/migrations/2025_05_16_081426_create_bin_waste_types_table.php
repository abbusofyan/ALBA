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
        Schema::create('bin_waste_types', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('bin_id');
			$table->foreign('bin_id')->references('id')->on('bins')->onDelete('cascade');
			$table->string('waste_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bin_waste_types');
    }
};
