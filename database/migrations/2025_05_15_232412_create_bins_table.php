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
        Schema::create('bins', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('bin_type_id');
			$table->foreign('bin_type_id')->references('id')->on('bin_types')->onDelete('cascade');
			$table->string('address');
			$table->string('lat');
			$table->string('long');
			$table->string('map_radius')->nullable();
			$table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bins');
    }
};
