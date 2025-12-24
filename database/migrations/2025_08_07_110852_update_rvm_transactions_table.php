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
		Schema::table('rvm_transactions', function (Blueprint $table) {
            $table->dropColumn(['rvm_id', 'store_id', 'receipt_number', 'barcode', 'total_deposit', 'redeemed']);

            $table->unsignedBigInteger('bin_id')->after('id');
            $table->string('rvm_type')->after('bin_id');
            $table->string('qrcode')->after('rvm_type');
            $table->json('data')->nullable()->after('qrcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::table('rvm_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('rvm_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('total_deposit', 10, 2)->nullable();
            $table->boolean('redeemed')->default(false);

            $table->dropColumn(['bin_id', 'rvm_type', 'qrcode', 'data']);
        });
    }
};
