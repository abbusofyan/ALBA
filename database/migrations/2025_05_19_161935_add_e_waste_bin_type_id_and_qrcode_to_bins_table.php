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
        Schema::table('bins', function (Blueprint $table) {
			$table->unsignedBigInteger('e_waste_bin_type_id')->nullable();
            $table->string('qrcode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bins', function (Blueprint $table) {
            $table->dropColumn(['e_waste_bin_type_id', 'qrcode']);
        });
    }
};
