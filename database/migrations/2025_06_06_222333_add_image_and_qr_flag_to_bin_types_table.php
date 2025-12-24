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
        Schema::table('bin_types', function (Blueprint $table) {
			$table->string('image')->nullable()->after('name'); // Adjust 'after' as needed
            $table->boolean('fixed_qrcode')->default(false)->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bin_types', function (Blueprint $table) {
            $table->dropColumn(['image', 'fixed_qrcode']);
        });
    }
};
