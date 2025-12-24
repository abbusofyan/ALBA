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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('event_type_id');
			$table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('cascade');
			$table->unsignedBigInteger('district_id')->nullable();
			$table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
			$table->unsignedBigInteger('user_id')->nullable();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->string('name')->nullable();
			$table->string('address');
			$table->string('lat');
			$table->string('long');
			$table->date('date_start');
			$table->date('date_end');
			$table->time('time_start');
			$table->time('time_end');
			$table->text('description')->nullable();
			$table->string('image')->nullable();
			$table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
