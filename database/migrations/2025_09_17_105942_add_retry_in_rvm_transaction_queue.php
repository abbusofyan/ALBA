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
        Schema::table('rvm_transaction_queues', function (Blueprint $table) {
            $table->integer('retry')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rvm_transaction_queues', function (Blueprint $table) {
            $table->dropColumn('retry');
        });
    }
};
