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
        Schema::create('event_waste_types', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('event_id');
			$table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
			$table->unsignedBigInteger('waste_type_id');
			$table->foreign('waste_type_id')->references('id')->on('waste_types')->onDelete('cascade');
			$table->decimal('price', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_waste_types');
    }
};
