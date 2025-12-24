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
        Schema::create('pick_up_orders', function (Blueprint $table) {
            $table->id();
			$table->foreignId('waste_type_id');
			$table->integer('min_weight')->nullable();
			$table->integer('max_weight')->nullable();
			$table->integer('quantity')->nullable();
			$table->string('e_waste_description')->nullable();
			$table->string('photo')->nullable();
			$table->string('remark')->nullable();
			$table->string('pickup_date');
			$table->string('pickup_start_time');
			$table->string('pickup_end_time');
			$table->string('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pick_up_orders');
    }
};
