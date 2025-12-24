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
        Schema::create('rvm_transaction_queues', function (Blueprint $table) {
            $table->id();
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('bin_id');
			$table->string('qrcode');
			$table->json('last_result');
			$table->string('type');
			$table->integer('status')->default(0);
            $table->timestamps();
			$table->unique('qrcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rvm_transaction_queues');
    }
};
